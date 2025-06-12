# tmux-omb

My personal configuration setup for **tmux** and **oh-my-bash**.

---

## What does it do?

This repository provides:

- **Custom tmux configuration:**  
  - Enhances multitasking with advanced keybindings, themes, and automatic session logging.
  - Makes pane and window management faster and more intuitive.
- **oh-my-bash configuration:**  
  - Modernizes and streamlines your Bash shell with themes, completions, plugins, and a huge set of productivity aliases.
- **Helpful notes:**  
  - Quick-reference guide for tmux shortcuts (`tmux-note.md`).

---

## Purpose

To maximize productivity and comfort for users who spend a lot of time in the terminal. The configs add quality-of-life improvements, speed up common tasks, and offer a beautiful, informative shell and tmux experience right out of the box.

---

## Features

### 🖥️ tmux (`.tmux.conf`)
- **Prefix key set to `Ctrl + A`** (like GNU screen)
- Advanced keybindings for pane/window management and navigation
- Vi-style copy mode
- Colorful, informative status bar with time and session info
- Mouse support (can be toggled)
- **Automatic pane logging:**  
  Each pane’s output is auto-logged to `/opt/tmux-logging` with timestamps for easy review.
- Quick window switching with `Alt + [1-5]`
- Handy prompt bindings for joining/sending panes

### 🐚 oh-my-bash (`.bashrc`)
- Loads the “font” theme for a modern look
- Useful completions and plugins (`git`, `bashmarks`, etc.)
- Extensive set of aliases for:
  - Networking, hacking, and pentesting tools (`nmap_basic`, `hackthebox`, `tryhackme`, `safenet`, etc.)
  - Productivity (`ll`, `la`, `gs`, `gp`, etc.)
  - Quick HTTP servers, IP detection, clipboard management, and more
- Custom welcome message and system info on terminal startup

### 📝 tmux Quick Reference (`tmux-note.md`)
- Cheat sheet of essential tmux shortcuts for window, pane, and text management

---

## Getting Started

1. **Clone the repository:**
   ```bash
   git clone https://github.com/SHAHID-XT/tmux-omb.git
   cd tmux-omb
   ```

2. **Backup your current configs (optional but recommended):**
   ```bash
   cp ~/.bashrc ~/.bashrc.bak
   cp ~/.tmux.conf ~/.tmux.conf.bak
   ```

3. **Copy the configs:**
   ```bash
   cp .bashrc ~/
   cp .tmux.conf ~/
   ```

4. **Restart your terminal and tmux:**
   - For Bash: `source ~/.bashrc`
   - For tmux: Restart tmux or run `tmux source-file ~/.tmux.conf`

---

## Requirements

- [tmux](https://github.com/tmux/tmux)
- [oh-my-bash](https://github.com/ohmybash/oh-my-bash)
- Common CLI tools: `exa`, `xclip`, `figlet`, `lolcat`, etc. (install as needed for some aliases)

---
