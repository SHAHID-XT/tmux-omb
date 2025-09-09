if [ -f /etc/bash_completion ]; then
    . /etc/bash_completion
fi

export OSH=$HOME/.oh-my-bash
OSH_THEME="ht"


#encoding fix
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8



OMB_USE_SUDO=true

completions=(
  git
  composer
  ssh
)


plugins=(
  git
  bashmarks
)



source "$OSH"/oh-my-bash.sh

alias ohmybash="mate ~/.oh-my-bash"
alias disk_check="qdirstat"
alias tryhackme="sudo killall openvpn;sudo openvpn /home/xt/Documents/TryHackMe/bOkA.ovpn"
alias hackthebox='sudo killall openvpn; sudo openvpn /home/xt/Documents/HackTheBox/starting_point_shahidxt.ovpn'
alias hibernate='systemctl hibernate'
alias nmap_basic='mkdir scans; nmap -sC -T4 -sV -oN scans/nmap_initial'
alias nmap_all_ports='mkdir scans; nmap -sC -T4 -sV -p- -oN scans/nmap_all_ports'
alias please='sudo'
alias safenet='proxychains firefox'
alias sl=ls
alias torcheck='curl --socks5 localhost:9050 --socks5-hostname localhost:9050 -s https://check.torproject.org/ | cat | grep -m 1 Congratulations | xargs'
alias up="ip --br a | grep tun0 | awk '{print \$3}' | cut -d '/' -f1; echo $PWD; python3 -m http.server 80"
alias uplocal="ip addr | grep eth0 | grep inet | awk '{print \$2}' | cut -d '/' -f1; pwd; python -m http.server"
alias copy='xclip -selection clipboard -i $@'
alias rustscan='sudo docker run -it --rm --name rustscan rustscan/rustscan:latest'
alias install='sudo apt install'
alias myip='echo "Public Ip : $(dig +short txt ch whoami.cloudflare @1.0.0.1 | cut -d \" -f 2)"'
alias ipconfig="ifconfig"
alias tb="nc termbin.com 9999"

alias rr='curl -s -L https://raw.githubusercontent.com/keroserene/rickrollrc/master/roll.sh | bash'
alias cl="clear && echo "bOkA" | h1 | cool"
alias ..='cd ..'
alias ...='cd ../../../'
alias ....='cd ../../../../'
alias ls='exa'
alias ll='exa -lhl'
alias la='exa -la'
alias sniff='sudo tcpdump -i tun0'
alias ports='netstat -tulanp'
alias grep='grep --color=auto'
alias egrep='egrep --color=auto'
alias fgrep='fgrep --color=auto'
alias findfile='find . -type f | grep'
alias editbash='vi ~/.bashrc'
alias reloadbash='source ~/.bashrc'
alias reloadzsh='source ~/.zshrc'
alias psg='ps aux | grep'
alias nano='nano -w'
alias vi='vim'
alias myip='curl http://ipinfo.io/ip'
alias flushdns='sudo systemd-resolve --flush-caches'
alias update='sudo apt-get update && sudo apt-get upgrade'
alias urlencode='python -c "import sys, urllib.parse as ul; print(ul.quote_plus(sys.argv[1]))"'
alias urldecode='python -c "import sys, urllib.parse as ul; print(ul.unquote_plus(sys.argv[1]))"'
alias nmap='nmap -v'
alias nikto='nikto -h'
alias untar='tar -zxvf'
alias unzip='unzip -o'
alias clone='git clone'
alias gs='git status'
alias ga='git add'
alias gb='git branch'
alias gc='git commit'
alias gp='git push'
alias sshkey='cat ~/.ssh/id_rsa.pub | xclip -selection c'
alias chmodx='chmod +x'
alias ducks='du -cks * | sort -rn | head'
alias strings='strings -a'
alias hexdump='xxd'
alias open="xdg-open"
alias chromium="chromium --no-sandbox"
alias j=john $1 --wordlist=/usr/share/wordlists/rockyou.txt
alias fix="stty -a | sed 's/;//g' | head -n 1 | sed 's/.*baud /s│ tty /g;s/line.*//g' | xclip -sel clip && stty raw -echo; fg"
alias fix=""
alias lab="hackthebox &>/dev/null & burpsuite &>/dev/null & firefox &>/dev/null &"
alias t="thunar"
alias htb="cd /htb"
alias powershell="pwsh"
alias thm="cd /thm"
alias winhost="sudo vi /mnt/c/Windows/System32/drivers/etc/hosts"
alias g="sudo ghidra"
alias postgres="sudo service postgresql restart"
alias msfconsole="sudo msfconsole"
alias cls="clear"
alias cool="lolcat"
alias cool1="cmatrix | lolcat"
alias h1="figlet"
alias quote="fortune"
alias slowtype="pv -qL 10"
alias clear="clear"
alias c="clear && echo "bOkA" | h1 | cool"















clear

RED='\033[1;31m'
GRN='\033[1;32m'
YEL='\033[1;33m'
BLU='\033[1;34m'
PRP='\033[1;35m'
CYN='\033[1;36m'
WHT='\033[1;37m'
RESET='\033[0m'


# Pick a random quote from /opt/.quotes
if [[ -f /opt/.quotes ]]; then
    RANDOM_QUOTE=$(shuf -n 1 /opt/.quotes)
else
    RANDOM_QUOTE="Your VM is ready, are you?"
fi

IST_TIME=$(TZ=Asia/Kolkata date '+%I:%M:%S %p')

echo -e "${RED}╔═══════════════════════════════════════════╗${RESET}"
echo -e "${RED}║${RESET} ${PRP}⛧ ${GRN} - Born in /tmp. Raised in /root. - ${RESET} ${PRP} ⛧${GRN}"
echo -e "${RED}╚═══════════════════════════════════════════╝${RESET}"

echo -e "${CYN}⟶${RESET} ${YEL}Date     :${RESET} $(TZ=Asia/Kolkata date '+%A %d %B %Y')"
echo -e "${CYN}⟶${RESET} ${YEL}Time     :${RESET} ${IST_TIME} ${CYN}(IST)${RESET}"
echo -e "${CYN}⟶${RESET} ${YEL}User     :${RESET} $USER"
echo -e "${CYN}⟶${RESET} ${YEL}IP       :${RESET} 127.0.0.1"
echo -e "${CYN}⟶${RESET} ${YEL}Uptime   :${RESET} $(uptime -p)"
echo ""
echo -e "${GRN}⚔  ${RANDOM_QUOTE}${RESET}"
echo ""
















# Auto-start tmux: attach to first session or create one if none exist
#if command -v tmux &>/dev/null && [ -z "$TMUX" ] && [ -n "$PS1" ]; then
#  tmux has-session 2>/dev/null && \
#    tmux attach-session -t "$(tmux ls | head -n 1 | cut -d: -f1)" || \
#    tmux new -s "bOkA "
#fi

export PATH=$PATH:$(go env GOPATH)/bin
