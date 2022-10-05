# Path to your oh-my-zsh configuration.
ZSH=$HOME/.oh-my-zsh

# Set name of the theme to load.
# Look in ~/.oh-my-zsh/themes/
# Optionally, if you set this to "random", it'll load a random theme each
# time that oh-my-zsh is loaded.
ZSH_THEME="jamescrunch"

# Example aliases
# alias zshconfig="mate ~/.zshrc"
# alias ohmyzsh="mate ~/.oh-my-zsh"

# Add an alias for a global composer, which we can easily use ahead of any
# project-specific composer. This is necessary because our $PATH would pick the
# project-specific composer script first.
alias gcomposer="/usr/local/bin/composer"

# Set to this to use case-sensitive completion
# CASE_SENSITIVE="true"

# Comment this out to disable weekly auto-update checks
# DISABLE_AUTO_UPDATE="true"

# Uncomment following line if you want to disable colors in ls
# DISABLE_LS_COLORS="true"

# Uncomment following line if you want to disable autosetting terminal title.
# DISABLE_AUTO_TITLE="true"

# Uncomment following line if you want red dots to be displayed while waiting for completion
 COMPLETION_WAITING_DOTS="true"

bgnotify_threshold=15

# Which plugins would you like to load? (plugins can be found in ~/.oh-my-zsh/plugins/*)
# Custom plugins may be added to ~/.oh-my-zsh/custom/plugins/
# Example format: plugins=(rails git textmate ruby lighthouse)
plugins=(git compleat git-flow extract history-substring-search last-working-dir drush bgnotify)

source $ZSH/oh-my-zsh.sh

# Not sure which of these is correct!
unsetopt correct
DISABLE_CORRECTION="true"
setopt nocorrectall
unsetopt correct_all

# Customize to your needs...
#export PATH=$HOME/.phpenv/bin:$HOME/.phpenv/plugins/php-build/bin:$PATH
export PATH=$HOME/.nodenv/bin:$HOME/.nodenv/plugins/node-build/bin:$PATH

#eval "$(phpenv init -)"
eval "$(nodenv init -)"

export PATH=bin:./node_modules/.bin:./vendor/bin:$HOME/.composer/vendor/bin:$PATH

export COMPOSER_EXIT_ON_PATCH_FAILURE=1
export DISABLE_PANTHEON_DRUSH_VERSION_WARNING=1

# Use mamp pro's version of PHP, as advised by CM's mamp pro guide.
MY_MAMP_PRO_PHP_VERSION="7.4.21"
export PATH="/Applications/MAMP/bin/php/php$MY_MAMP_PRO_PHP_VERSION/bin:$PATH"
alias php='/Applications/MAMP/bin/php/php$MY_MAMP_PRO_PHP_VERSION/bin/php -c "/Library/Application Support/appsolute/MAMP PRO/conf/php$MY_MAMP_PRO_PHP_VERSION.ini"'
export PHPRC="/Library/Application Support/appsolute/MAMP PRO/conf/php$MY_MAMP_PRO_PHP_VERSION.ini"
# Plus everything else that mamp pro provides/uses, e.g. mysql.
export PATH=$PATH:/Applications/MAMP/Library/bin

export HOMEBREW_NO_AUTO_UPDATE=1

# BEGIN SNIPPET: Platform.sh CLI configuration
export PATH="$HOME/"'.platformsh/bin':"$PATH"
if [ -f "$HOME/"'.platformsh/shell-config.rc' ]; then . "$HOME/"'.platformsh/shell-config.rc'; fi # END SNIPPET
