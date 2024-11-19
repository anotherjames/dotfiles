# Path to your oh-my-zsh configuration.
ZSH=$HOME/.oh-my-zsh

# Set name of the theme to load.
# Look in ~/.oh-my-zsh/themes/
# Optionally, if you set this to "random", it'll load a random theme each
# time that oh-my-zsh is loaded.
ZSH_THEME="jamescrunch"

# Add an alias for a global composer, which we can easily use ahead of any
# project-specific composer. This is necessary because our $PATH would pick the
# project-specific composer script first.
alias gcomposer="/usr/local/bin/composer"

# Set to this to use case-sensitive completion
# CASE_SENSITIVE="true"

# Comment this out to disable weekly auto-update checks
# DISABLE_AUTO_UPDATE="true"

zstyle ':omz:update' mode auto

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
plugins=(history-substring-search last-working-dir drush bgnotify)
# Plugins previously enabled: git compleat extract

source $ZSH/oh-my-zsh.sh

# Not sure which of these is correct!
unsetopt correct
DISABLE_CORRECTION="true"
setopt nocorrectall
unsetopt correct_all

# Customize to your needs...
#export PATH=$HOME/.phpenv/bin:$HOME/.phpenv/plugins/php-build/bin:$PATH
export PATH=$HOME/.nodenv/bin:$HOME/.nodenv/plugins/node-build/bin:$PATH

#export RUBY_CONFIGURE_OPTS="--with-openssl-dir=$(brew --prefix openssl@1.1) --with-readline-dir=$(brew --prefix readline) --with-libyaml-dir=$(brew --prefix libyaml) --with-zlib-dir=$(brew --prefix zlib)"

#eval "$(rbenv init -)"
#eval "$(phpenv init -)"
eval "$(nodenv init -)"
eval "$(pyenv init -)"

export PATH=$HOME/.pyenv/shims:bin:./node_modules/.bin:./vendor/bin:$HOME/.composer/vendor/bin:$HOME/.mampenv/bin:$PATH

export COMPOSER_EXIT_ON_PATCH_FAILURE=1
export DISABLE_PANTHEON_DRUSH_VERSION_WARNING=1

#export PATH="/usr/local/opt/openssl@1.1/bin:$PATH"

# Included in an attempt to get `rbenv exec gem install bundler` working
#export LDFLAGS="-L/usr/local/opt/zlib/lib"
#export CPPFLAGS="-I/usr/local/opt/zlib/include"
#export PKG_CONFIG_PATH="/usr/local/opt/zlib/lib/pkgconfig"

export HOMEBREW_NO_AUTO_UPDATE=1

# BEGIN SNIPPET: Platform.sh CLI configuration
HOME=${HOME:-'/Users/jameswilliams'}
export PATH="$HOME/"'.platformsh/bin':"$PATH"
if [ -f "$HOME/"'.platformsh/shell-config.rc' ]; then . "$HOME/"'.platformsh/shell-config.rc'; fi # END SNIPPET
