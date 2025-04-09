# Enable the subsequent settings only in interactive sessions
case $- in
  *i*) ;;
    *) return;;
esac

# Path to your oh-my-bash installation.
export OSH='/home/boka/.oh-my-bash'

# Set name of the theme to load. Optionally, if you set this to "random"
# it'll load a random theme each time that oh-my-bash is loaded.
OSH_THEME="rjorgenson"

# If you set OSH_THEME to "random", you can ignore themes you don't like.
# OMB_THEME_RANDOM_IGNORED=("powerbash10k" "wanelo")

# Uncomment the following line to use case-sensitive completion.
# OMB_CASE_SENSITIVE="true"

# Uncomment the following line to use hyphen-insensitive completion. Case
# sensitive completion must be off. _ and - will be interchangeable.
# OMB_HYPHEN_SENSITIVE="false"

# Uncomment the following line to disable bi-weekly auto-update checks.
# DISABLE_AUTO_UPDATE="true"

# Uncomment the following line to change how often to auto-update (in days).
# export UPDATE_OSH_DAYS=13

# Uncomment the following line to disable colors in ls.
# DISABLE_LS_COLORS="true"

# Uncomment the following line to disable auto-setting terminal title.
# DISABLE_AUTO_TITLE="true"

# Uncomment the following line to enable command auto-correction.
ENABLE_CORRECTION="true"

# Uncomment the following line to display red dots whilst waiting for completion.
# COMPLETION_WAITING_DOTS="true"

# Uncomment the following line if you want to disable marking untracked files
# under VCS as dirty. This makes repository status check for large repositories
# much, much faster.
# DISABLE_UNTRACKED_FILES_DIRTY="true"

# Uncomment the following line if you don't want the repository to be considered dirty
# if there are untracked files.
# SCM_GIT_DISABLE_UNTRACKED_DIRTY="true"

# Uncomment the following line if you want to completely ignore the presence
# of untracked files in the repository.
# SCM_GIT_IGNORE_UNTRACKED="true"

# Uncomment the following line if you want to change the command execution time
# stamp shown in the history command output.  One of the following values can
# be used to specify the timestamp format.
# * 'mm/dd/yyyy'     # mm/dd/yyyy + time
# * 'dd.mm.yyyy'     # dd.mm.yyyy + time
# * 'yyyy-mm-dd'     # yyyy-mm-dd + time
# * '[mm/dd/yyyy]'   # [mm/dd/yyyy] + [time] with colors
# * '[dd.mm.yyyy]'   # [dd.mm.yyyy] + [time] with colors
# * '[yyyy-mm-dd]'   # [yyyy-mm-dd] + [time] with colors
# If not set, the default value is 'yyyy-mm-dd'.
# HIST_STAMPS='yyyy-mm-dd'

# Uncomment the following line if you do not want OMB to overwrite the existing
# aliases by the default OMB aliases defined in lib/*.sh
# OMB_DEFAULT_ALIASES="check"

# Would you like to use another custom folder than $OSH/custom?
# OSH_CUSTOM=/path/to/new-custom-folder

# To disable the uses of "sudo" by oh-my-bash, please set "false" to
# this variable.  The default behavior for the empty value is "true".
OMB_USE_SUDO=true

# To enable/disable display of Python virtualenv and condaenv
# OMB_PROMPT_SHOW_PYTHON_VENV=true  # enable
# OMB_PROMPT_SHOW_PYTHON_VENV=false # disable

# Which completions would you like to load? (completions can be found in ~/.oh-my-bash/completions/*)
# Custom completions may be added to ~/.oh-my-bash/custom/completions/
# Example format: completions=(ssh git bundler gem pip pip3)
# Add wisely, as too many completions slow down shell startup.
completions=(
  git
  composer
  ssh
)

# Which aliases would you like to load? (aliases can be found in ~/.oh-my-bash/aliases/*)
# Custom aliases may be added to ~/.oh-my-bash/custom/aliases/
# Example format: aliases=(vagrant composer git-avh)
# Add wisely, as too many aliases slow down shell startup.
aliases=(
  general
)

# Which plugins would you like to load? (plugins can be found in ~/.oh-my-bash/plugins/*)
# Custom plugins may be added to ~/.oh-my-bash/custom/plugins/
# Example format: plugins=(rails git textmate ruby lighthouse)
# Add wisely, as too many plugins slow down shell startup.
plugins=(
  git
  bashmarks
)

# Which plugins would you like to conditionally load? (plugins can be found in ~/.oh-my-bash/plugins/*)
# Custom plugins may be added to ~/.oh-my-bash/custom/plugins/
# Example format:
if [ "$DISPLAY" ] || [ "$SSH" ]; then
      plugins+=(tmux-autoattach)
  fi


if command -v tmux &> /dev/null && [ -z "$TMUX" ]; then
    tmux attach || tmux new-session
fi



source "$OSH"/oh-my-bash.sh

# User configuration
# export MANPATH="/usr/local/man:$MANPATH"

# You may need to manually set your language environment
# export LANG=en_US.UTF-8

# Preferred editor for local and remote sessions
# if [[ -n $SSH_CONNECTION ]]; then
#   export EDITOR='vim'
# else
#   export EDITOR='mvim'
# fi

# Compilation flags
# export ARCHFLAGS="-arch x86_64"

# ssh
# export SSH_KEY_PATH="~/.ssh/rsa_id"

# Set personal aliases, overriding those provided by oh-my-bash libs,
# plugins, and themes. Aliases can be placed here, though oh-my-bash
# users are encouraged to define aliases within the OSH_CUSTOM folder.
# For a full list of active aliases, run `alias`.
#
# Example aliases
# alias bashconfig="mate ~/.bashrc"
alias ohmybash="mate ~/.oh-my-bash"
#alias blackarch_data='sudo pacman -Sg | grep blackarch'
alias disk_check="qdirstat"
alias tryhackme="sudo killall openvpn;sudo openvpn /home/xt/Documents/TryHackMe/bOkA.ovpn"
alias hackthebox='sudo killall openvpn; sudo openvpn /home/xt/Documents/HackTheBox/starting_point_shahidxt.ovpn'
alias hibernate='systemctl hibernate'
#alias nc="ncat"
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
# termbin
alias tb="nc termbin.com 9999"

# the terminal rickroll
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
# alias myip='curl http://ipinfo.io/ip'
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
# alias up="ip -f inet addr show tun0 | echo \"http://\`grep -Po 'inet \K[\d.]+'\`/\"; echo $PWD; sudo python3 -m http.server 80"
alias ducks='du -cks * | sort -rn | head'
alias strings='strings -a'
alias hexdump='xxd'
alias open="xdg-open"
# alias vim="micro"
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
echo ""
echo -e "\033[1;31m \033[0m"
echo -e "\033[1;31m Welcome, bOkAxT! Today is: $(date) \033[0m"
echo -e "\033[1;34m Your IP Address: 127.0.0.1 \033[0m"
echo -e "\033[1;32m Uptime: $(uptime -p) \033[0m"
echo -e "\033[1;33m Logged in as: $USER \033[0m"
echo ""


# Optional: Cow says something dumb
if command -v cowsay &> /dev/null; then
    fortune | cowsay | cool
fi

echo ""

