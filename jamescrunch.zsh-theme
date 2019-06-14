# CRUNCH - created from Steve Eley's cat waxing.
# Initially hacked from the Dallas theme. Thanks, Dallas Reedy.
#
# This theme assumes you do most of your oh-my-zsh'ed "colorful" work at a single machine, 
# and eschews the standard space-consuming user and hostname info.  Instead, only the 
# things that vary in my own workflow are shown:
#
# * The time (not the date)
# * The RVM version and gemset (omitting the 'ruby' name if it's MRI)
# * The current directory
# * The Git branch and its 'dirty' state
# 
# Colors are at the top so you can mess with those separately if you like.
# For the most part I stuck with Dallas's.

# Some handy debugging-like characters: ͯϞϟϰЖжѪѫӜӝӫӪ⍾
function xdebug_status {
    [ ! -z "${XDEBUG_CONFIG+set}" ] && echo 'ϟ '
}

CRUNCH_BRACKET_COLOR="%{$fg[white]%}"
CRUNCH_TIME_COLOR="%{$fg[yellow]%}"
CRUNCH_RVM_COLOR="%{$fg[magenta]%}"
CRUNCH_DIR_COLOR="%{$fg[cyan]%}"
CRUNCH_GIT_BRANCH_COLOR="%{$fg[green]%}"
CRUNCH_GIT_CLEAN_COLOR="%{$fg[green]%}"
CRUNCH_GIT_DIRTY_COLOR="%{$fg[red]%}"
JAMES_DRUSH_COLOR="%{$fg_bold[blue]%}"

# These Git variables are used by the oh-my-zsh git_prompt_info helper:
ZSH_THEME_GIT_PROMPT_PREFIX="$CRUNCH_BRACKET_COLOR:$CRUNCH_GIT_BRANCH_COLOR"
ZSH_THEME_GIT_PROMPT_SUFFIX=""
ZSH_THEME_GIT_PROMPT_CLEAN=" $CRUNCH_GIT_CLEAN_COLOR✓"
ZSH_THEME_GIT_PROMPT_DIRTY=" $CRUNCH_GIT_DIRTY_COLOR✗"

# Our elements:
CRUNCH_TIME_="$CRUNCH_BRACKET_COLOR{$CRUNCH_TIME_COLOR%T$CRUNCH_BRACKET_COLOR}%{$reset_color%}"
#if [ -e ~/.rvm/bin/rvm-prompt ]; then
#  CRUNCH_RVM_="$CRUNCH_BRACKET_COLOR"["$CRUNCH_RVM_COLOR\${\$(~/.rvm/bin/rvm-prompt i v g)#ruby-}$CRUNCH_BRACKET_COLOR"]"%{$reset_color%}"
#else
#  if which rbenv &> /dev/null; then
#    CRUNCH_RVM_="$CRUNCH_BRACKET_COLOR"["$CRUNCH_RVM_COLOR\${\$(rbenv version | sed -e 's/ (set.*$//' -e 's/^ruby-//')}$CRUNCH_BRACKET_COLOR"]"%{$reset_color%}"
#  fi
#fi
# I would like to show the current drush alias in use, if possible, but it doesn't seem to work
# as it gets stuck on what it was on launching zsh rather than dynamically updating.
#CRUNCH_DIR_="$CRUNCH_DIR_COLOR%~\$JAMES_DRUSH_COLOR$(drupalsite)%{$reset_color%}$(git_prompt_info) "
CRUNCH_DIR_="$CRUNCH_DIR_COLOR%~\$(git_prompt_info) "
CRUNCH_PROMPT="$CRUNCH_BRACKET_COLOR➭ "

# Put it all together!
#PROMPT="$CRUNCH_TIME_$CRUNCH_RVM_$CRUNCH_DIR_$CRUNCH_PROMPT%{$reset_color%}"
#PROMPT="$CRUNCH_TIME_$CRUNCH_DIR_$CRUNCH_PROMPT%{$reset_color%}"

# From https://github.com/drush-ops/drush/blob/e03a3ef04f559f7ec00df637b4035e09c9cf389e/drush.complete.sh#L18
# which I just couldn't get to work out of the box :-(
__drush_ps1() {
  f="${TMPDIR:-/tmp/}/drush-env-${USER}/drush-drupal-site-$$"
  if [ -f $f ]
  then
    __DRUPAL_SITE=$(cat "$f")
  else
    __DRUPAL_SITE="$DRUPAL_SITE"
  fi

  # Set DRUSH_PS1_SHOWCOLORHINTS to a non-empty value and define a
  # __drush_ps1_colorize_alias() function for color hints in your Drush PS1
  # prompt. See example.prompt.sh for an example implementation.
  if [ -n "${__DRUPAL_SITE-}" ] && [ -n "${DRUSH_PS1_SHOWCOLORHINTS-}" ]; then
    __drush_ps1_colorize_alias
  fi

  [[ -n "$__DRUPAL_SITE" ]] && printf "${1:- (%s)}" "$__DRUPAL_SITE"
}

# We now do it like ST's custom steeef.
# drupal_site is in the official oh-my-zsh drush plugin.
PROMPT=$'%{$fg[white]%}{%{$fg[yellow]%}%T%{$fg[white]%}}%{$fg[cyan]%}%~%{$fg_bold[blue]%}$(__drush_ps1)%{$reset_color%}$(git_prompt_info) %{$fg_bold[yellow]%}$(xdebug_status)%{$fg_no_bold[white]%}➭ %{$reset_color%}'
