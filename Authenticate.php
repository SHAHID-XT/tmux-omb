<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Authenticate.php,v 1.10 2005/02/28 05:25:22 jack Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Users/Users.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');
require_once('include/logging.php');
require_once('user_privileges/audit_trail.php');
require_once('plugins/2FactorGoogleAuth/GoogleAuthenticator.php');
require_once('plugins/2FactorDuoAuth/Web.php');

require_once('include/utils/jcryption.php');

global $mod_strings, $default_charset, $adb, $CONCURRENT_SESSION_WEB_APP;

$focus = new Users();

//If Auto login from outlook to pecrm is on and param is not empty.
if($AUTO_LOGIN_FROM_OUTLOOK_TO_PECRM == 'on' && !empty($_REQUEST['param'])){
	
	$ecrypt_user_password = $_REQUEST['user_password'];
	$param_arr = explode("#$$#",urldecode($_REQUEST['param']));
	
	$user_id = !empty($param_arr[1]) ? $param_arr[1] : '';
	$user_query = $adb->query($que = "SELECT user_name, decry_keys FROM vtiger_users WHERE id='".$user_id."'");
	$user_arr = $adb->fetch_array($user_query);

	$user_name  = $user_arr['user_name']; 
	$decry_keys  = $user_arr['decry_keys']; 
	
	$decry_keys_obj = json_decode(html_entity_decode($decry_keys));
	$jCryption = new jCryption();
	
	$var = $jCryption->decrypt($param_arr[0], $decry_keys_obj->d->int, $decry_keys_obj->n->int);

	// Add in defensive code here.
	$focus->column_fields["user_name"] = to_html($user_name);
	$user_password = '';
	
	$_REQUEST['ol_login_key'] = !empty($var) ? $var : '';

	$focus->load_user($user_password,$_REQUEST['ol_login_key']);
	
}else{
	$ecrypt_user_password = $_REQUEST['user_password'];

	$jCryption = new jCryption();
	$var = $jCryption->decrypt($_REQUEST['user_password'], $_SESSION["d"]["int"], $_SESSION["n"]["int"]);

	$_REQUEST['user_password'] = urldecode($var);

	// Add in defensive code here.
	$focus->column_fields["user_name"] = to_html($_REQUEST['user_name']);
	$user_password = $_REQUEST['user_password'];

	$focus->load_user($user_password,$_REQUEST['ol_login_key']);

	if(ENABLE_CAPTCHA){
		// $secret = GOOGLE_SECRET_KEY;
		// $recaptchaResponse = $_POST['g-recaptcha-response'];
		// $userIp = getenv("REMOTE_ADDR") ;
		// $url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$recaptchaResponse."&remoteip=".$userIp;
		// $ch = curl_init(); 
		// curl_setopt($ch, CURLOPT_URL, $url); 
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// // For localhost
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		// $output = curl_exec($ch); 
		// if (curl_errno($ch)) {
		// 	$error_msg = curl_error($ch);
		// }
		// curl_close($ch);      
		// $captcha_status= json_decode($output, true);

		// if (empty($recaptchaResponse)) {
		//     $captchaErrorMessage = "Sorry, we could not verify that you are a human. Please try again.";
		// }
	}

}

$successURL = 'index.php';

if($focus->is_authenticated()) {
	unset($_SESSION['attempts_error']);
	unset($_SESSION['login_error']);

	if(ENABLE_CAPTCHA && !$_REQUEST['param']){
		// if (!$captcha_status['success'] && !empty($captchaErrorMessage) && !isset($_SESSION['isGooglereCAPTCHAValidated']) ) {
		// 	session_destroy();
		// 	session_start();
        // 	$_SESSION['login_error'] = $captchaErrorMessage;
		// 	header("Location: index.php");
		// 	exit();
    	// }elseif(empty($_SESSION['isGooglereCAPTCHAValidated'])){
		// 	$_SESSION['isGooglereCAPTCHAValidated'] = TRUE;
		// }

	}

	session_regenerate_id();
	$authprocess="yes";
	$sessionid = session_id();
	if($two_factor_auth['totp'] == 1){
		$qr_code = $_REQUEST['qr_code'];
		$auth_q = $adb->query("SELECT google_auth_key FROM vtiger_users WHERE id='".$focus->id."'");
		$auth_fetch = $adb->fetch_array($auth_q);
		// Check if user has verified Google Authenticator Device
		if (!empty($auth_fetch['google_auth_key'])) {
			if (!empty($_REQUEST['auth']) && $_REQUEST['auth'] == 'Google2FA') {
				$ga = new GoogleAuthenticator();
				$checkResult = $ga->verifyCode($auth_fetch['google_auth_key'], $qr_code, 4);
				if ($checkResult) {
					$authprocess ="yes";
				} else {
					$authprocess ="no";
				}
			} else {
				require_once('modules/Users/2FA.php');
				die;
			}
		} 
    }else if($two_factor_auth['duo'] == 1){
		if($focus->column_fields['user_name'] == 'admin'){ //if condition for disable DUO for other users. If enable DUO for all user please remove this if condition
			if (isset($_REQUEST['sig_response'])) {
				$resp = Duo\Web::verifyResponse($DUO_IKEY, $DUO_SKEY, $DUO_AKEY, $_REQUEST['sig_response']);
				if ($resp === $focus->column_fields["user_name"]) {
					$authprocess ="yes";
				}else{
					$authprocess ="no";
				}
			}else{
				$sig_request = Duo\Web::signRequest($DUO_IKEY, $DUO_SKEY, $DUO_AKEY, $_REQUEST['user_name']);
				?>
				<script type="text/javascript" src="plugins/2FactorDuoAuth/Duo-Web-v2.js"></script>
				<link rel="stylesheet" type="text/css" href="plugins/2FactorDuoAuth/Duo-Frame.css">
				<title>2 Factor Authentication</title>
				<iframe id="duo_iframe"
					data-host="<?php echo $DUO_HOST; ?>"
					data-pefotoken="<?php echo $_REQUEST['__vt5rftk']; ?>"
					data-username="<?php echo $focus->column_fields["user_name"]; ?>"
					data-password="<?php echo $ecrypt_user_password; ?>"
					data-sig-request="<?php echo $sig_request; ?>"
				></iframe>
				<?php
				die();
			}
		}	
	}else if($two_factor_auth['authy'] == 1){
		if (isset($_REQUEST['authy_check'])) {
			if ($_REQUEST['authy_check'] == "yes") {
				$authprocess = "yes";
			} else {
				$authprocess = "no";
			}
		} else {
			require_once('modules/Users/Authy.php');
			die;
		}
	}
	
	if(isset($_SESSION['isGooglereCAPTCHAValidated'])){
		unset($_SESSION['isGooglereCAPTCHAValidated']);
	}
	
	$change_login_attempt = "UPDATE vtiger_user_login_attempt SET attempts = 0 WHERE user_id = '".$focus->id."'";
	$adb->query($change_login_attempt);
	if ($authprocess == 'yes'){
		if($audit_trail == 'true') {
			if($record == '')
				$auditrecord = '';
			else
				$auditrecord = $record;

			$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
			$query = "insert into vtiger_audit_trial values(?,?,?,?,?,?)";
			$params = array($adb->getUniqueID('vtiger_audit_trial'), $focus->id, 'Users','Authenticate','',$date_var);
			$adb->pquery($query, $params);
		}

		require_once('modules/Users/LoginHistory.php');
		// Recording the login info
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']!=''){
			$usip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$usip = $_SERVER['REMOTE_ADDR'];
		}
		$intime=date("Y/m/d H:i:s");
		$loghistory=new LoginHistory();
		$Signin = $loghistory->user_login($focus->column_fields["user_name"],$usip,$intime);
		
		// Concurrent session handling
		$appname = "WEB_APP";
		updateSessionDataForWebApp($focus->column_fields["user_name"], $sessionid, $appname, $usip);
	
		//Security related entries start
		require_once('include/utils/UserInfoUtil.php');

		/*
			An issue occurs- when a client uses two servers for sharing load. If a user is created on one server, the user and sharing privilege file are not created on the other server.
			To create user and share privilege file at the time of login. We are using below code.
			If "sharing_privileges_{userid}" file is not found in "user_privileges" folder then create "user and sharing privilege" file else create only user_privilege file. 
		*/
		if(!file_exists('user_privileges/sharing_privileges_' . $focus->id . '.php')){
			// Recalculate for the current user.
			RecalculateSharingRules($focus->id);
		}else{
			createUserPrivilegesfile($focus->id);
		}

		//Security related entries end
		$_SESSION['authenticated_user_id'] = $focus->id;
		$_SESSION['AUTHUSERID'] = $focus->id;
		$_SESSION['app_unique_key'] = $application_unique_key;
		$_SESSION['parent_user'] = '';

		//Set Currency Applying Type in Seesion
		
		$q	= "SELECT currency_exchange_applying_type
				FROM vtiger_cfi_currency_exchange_config 
				LIMIT 0,1";
		
		$query 	= $adb->query($q);
		$num 	= $adb->num_rows($query);

		if($num > 0) {
			if($adb->query_result($query, 0, 'currency_exchange_applying_type') == '1'){
				$_SESSION["exchange_rate_table"] = 'daily_exchange_rate';	
			}else{
				$_SESSION["exchange_rate_table"] = 'date_specific_exchange_rate';	
			}
		} else {
			$_SESSION["exchange_rate_table"] = 'daily_exchange_rate';
		}
		
		global $upload_badext;
		//Enabled session variable for KCFINDER
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false;
		$_SESSION['KCFINDER']['uploadURL'] = "test/upload";
		$_SESSION['KCFINDER']['uploadDir'] = "../test/upload";
		$deniedExts = implode(" ", $upload_badext);
		$_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
		
		// store the user's theme in the session
		if(!empty($focus->column_fields["theme"])) {
			$authenticated_user_theme = $focus->column_fields["theme"];
		} else {
			$authenticated_user_theme = $default_theme;
		}

		// store the user's language in the session
		if(!empty($focus->column_fields["language"])) {
			$authenticated_user_language = $focus->column_fields["language"];
		} else {
			$authenticated_user_language = $default_language;
		}

		// If this is the default user and the default user theme is set to reset, reset it to the default theme value on each login
		if($reset_theme_on_default_user && $focus->user_name == $default_user_name) {
			$authenticated_user_theme = $default_theme;
		}
		if(isset($reset_language_on_default_user) && $reset_language_on_default_user && $focus->user_name == $default_user_name) {
			$authenticated_user_language = $default_language;
		}

		$_SESSION['vtiger_authenticated_user_theme'] = $authenticated_user_theme;
		$_SESSION['authenticated_user_language'] = $authenticated_user_language;

		$log->debug("authenticated_user_theme is $authenticated_user_theme");
		$log->debug("authenticated_user_language is $authenticated_user_language");
		$log->debug("authenticated_user_id is ". $focus->id);
		$log->debug("app_unique_key is $application_unique_key");

		// Clear all uploaded import files for this user if it exists
		global $import_dir;

		$tmp_file_name = $import_dir. "IMPORT_".$focus->id;

		if (file_exists($tmp_file_name)) {
			unlink($tmp_file_name);
		}
			$arr = $_SESSION['lastpage'];
			if(isset($_SESSION['lastpage'])) {
				//header("Location: $successURL".$arr);
				header("Location: ".$successURL."?".$arr);
			} else {
				header("Location: $successURL");
			}
	}else{
		$_SESSION['login_error'] = 'Authentication Failed. Please try again.';

		// go back to the login screen.
		// create an error message for the user.
		header("Location: index.php");
	}
	
} else {

	if(ENABLE_CAPTCHA && !$_REQUEST['param']){
		// if (!$captcha_status['success'] && !empty($captchaErrorMessage) && !isset($_SESSION['isGooglereCAPTCHAValidated']) ) {
		// 	session_destroy();
		// 	session_start();
        // 	$_SESSION['login_error'] = $captchaErrorMessage;
		// 	header("Location: index.php");
		// 	exit();
    	// }elseif(empty($_SESSION['isGooglereCAPTCHAValidated'])){
		// 	$_SESSION['isGooglereCAPTCHAValidated'] = TRUE;
		// }

	}
	
	$sql = 'select user_name, id, crypt_type, status from vtiger_users where user_name=?';
	$result = $adb->pquery($sql, array($focus->column_fields["user_name"]));

	$user_detail=$adb->fetch_array($result);
	$value = $user_detail['id'];
	
	//Display Access denied error msg if user is Inactive
	if ($user_detail["status"] == 'Inactive') 
	{ 
		$_SESSION['attempts_error']=$mod_strings['ERR_USER_BLOCKED'];
	}
	else if($user_detail["status"] == 'Active')
	{
		addLoginAttempt($value);
	}
	else{
		unset($_SESSION['attempts_error']);
	}
	
	$rowList = $result->GetRows();
	foreach ($rowList as $row) {
		$cryptType = $row['crypt_type'];
		/* PHP 5.3 WIN implementation of crypt API not compatible with earlier version */
		if(strtolower($cryptType) == 'md5' && version_compare(PHP_VERSION, '5.3.0') >= 0 && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
			header("Location: modules/Migration/PHP5.3_PasswordHelp.php");
			die;
		}
	}
	$_SESSION['login_user_name'] = $focus->column_fields["user_name"];
	$_SESSION['login_password'] = $user_password;
	$_SESSION['login_error'] = $mod_strings['ERR_INVALID_PASSWORD'];

	// go back to the login screen.
	// create an error message for the user.
	header("Location: index.php");
}

//If user is Active and user login with wrong password then no of wrong attempts increased in vtiger_user_login_attempt table
function addLoginAttempt($value){
	global $adb,$ATTEMPTS_NUMBER,$mod_strings;
	//Increase number of attempts. Set last login attempt if required.
	$q = "SELECT * FROM vtiger_user_login_attempt WHERE user_id = '".$value."'"; 
   $result = $adb->query($q);
   $data = $adb->fetch_array($result);
   
	if($data)
	{
		$attempts = $data["attempts"]+1;         
		if($attempts < $ATTEMPTS_NUMBER)
		{
			$_SESSION['attempts_error']=$mod_strings['ERR_ATTEMPTS1'].' '.$attempts.' '.$mod_strings['ERR_ATTEMPTS2'];
			$q = "UPDATE vtiger_user_login_attempt SET attempts=".$attempts.", lastlogin=NOW() WHERE user_id = '".$value."'";
			$result = $adb->query($q);
		}
		else if($attempts == $ATTEMPTS_NUMBER) {
			user_blocked($value);
			$_SESSION['attempts_error']=$mod_strings['ERR_ATTEMPTS3'];
		}
	}
	else {
		$_SESSION['attempts_error']=$mod_strings['ERR_ATTEMPTS1'].' 1 '.$mod_strings['ERR_ATTEMPTS2'];
		$q = "INSERT INTO vtiger_user_login_attempt (user_id, attempts,lastlogin) values ('".$value."', 1, NOW())";
		$result = $adb->query($q);
	}
}

function user_blocked($user_id) {
	global $adb;
	$q = "UPDATE vtiger_users SET status = 'Inactive' WHERE id = '".$user_id."'";
	return $adb->query($q);
}

function updateSessionDataForWebApp($username, $sessionid, $appname, $ip_address) {
	global $adb, $CONCURRENT_SESSION_WEB_APP;
	$create_date_time = date('Y-m-d H:i:s');
	$execute_update_query = "";
	$execute_insert_query = "";
	if (!empty($CONCURRENT_SESSION_WEB_APP) && $CONCURRENT_SESSION_WEB_APP == 'off') {
		$query = "SELECT * FROM pefo_associated_apps_session 
							WHERE username = '".$username."' AND app_name = '".$appname."'";
		$result_exist = $adb->query($query);
		if($adb->num_rows($result_exist) > 0) {
			$execute_update_query = $adb->query("UPDATE pefo_associated_apps_session
												 SET status = 'D' WHERE username = '".$username."'
												 AND app_name = '".$appname."'");
									
			$execute_insert_query = $adb->query("INSERT INTO pefo_associated_apps_session
												 SET username = '".$username."',
												 session_id = '".$sessionid."',
												 created_date = '".$create_date_time."',
												 app_name = '".$appname."',
												 ip = '".$ip_address."',
												 status = 'A'");
		} else {
			$execute_insert_query = $adb->query("INSERT INTO pefo_associated_apps_session
												 SET username = '".$username."',
												 session_id = '".$sessionid."',
												 created_date = '".$create_date_time."',
												 app_name = '".$appname."',
												 ip = '".$ip_address."',
												 status = 'A'");
		}
	} else {
		$execute_insert_query = $adb->query("INSERT INTO pefo_associated_apps_session
											 SET username = '".$username."',
											 session_id = '".$sessionid."',
											 created_date = '".$create_date_time."',
											 app_name = '".$appname."',
											 ip = '".$ip_address."',
											 status = 'A'");
	}
	return $result;
} 
?>
