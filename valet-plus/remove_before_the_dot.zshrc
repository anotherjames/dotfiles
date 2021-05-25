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

export RUBY_CONFIGURE_OPTS="--with-openssl-dir=$(brew --prefix openssl@1.1) --with-readline-dir=$(brew --prefix readline) --with-libyaml-dir=$(brew --prefix libyaml) --with-zlib-dir=$(brew --prefix zlib)"

eval "$(rbenv init -)"
#eval "$(phpenv init -)"
eval "$(nodenv init -)"

export PATH=bin:./node_modules/.bin:./vendor/bin:$HOME/.composer/vendor/bin:$PATH

export COMPOSER_EXIT_ON_PATCH_FAILURE=1
export DISABLE_PANTHEON_DRUSH_VERSION_WARNING=1

# Add versions of PHP for valet:
# @TODO Make this dynamic according to the version to currently use.
#export PATH="/usr/local/opt/valet-php@7.2/bin:$PATH"
#export PATH="/usr/local/opt/valet-php@7.2/sbin:$PATH"
export PATH="/usr/local/opt/openssl@1.1/bin:$PATH"
#export PATH="/usr/local/opt/valet-php@7.1/bin:$PATH"
#export PATH="/usr/local/opt/valet-php@7.1/sbin:$PATH"
#export PATH="/usr/local/opt/valet-php@7.3/bin:$PATH"
#export PATH="/usr/local/opt/valet-php@7.3/sbin:$PATH"
export PATH="/usr/local/opt/valet-php@7.4/bin:$PATH"
export PATH="/usr/local/opt/valet-php@7.4/sbin:$PATH"
#export PATH="/usr/local/opt/valet-php@5.6/bin:$PATH"
#export PATH="/usr/local/opt/valet-php@5.6/sbin:$PATH"

# Included in an attempt to get `rbenv exec gem install bundler` working
export LDFLAGS="-L/usr/local/opt/zlib/lib"
export CPPFLAGS="-I/usr/local/opt/zlib/include"
export PKG_CONFIG_PATH="/usr/local/opt/zlib/lib/pkgconfig"

function blt() {
  if [[ ! -z ${AH_SITE_ENVIRONMENT} ]]; then
    PROJECT_ROOT="/var/www/html/${AH_SITE_GROUP}.${AH_SITE_ENVIRONMENT}"
  elif [ "`git rev-parse --show-cdup 2> /dev/null`" != "" ]; then
    PROJECT_ROOT=$(git rev-parse --show-cdup)
  else
    PROJECT_ROOT="."
  fi

  if [ -f "$PROJECT_ROOT/vendor/bin/blt" ]; then
    $PROJECT_ROOT/vendor/bin/blt "$@"

  # Check for local BLT.
  elif [ -f "./vendor/bin/blt" ]; then
    ./vendor/bin/blt "$@"

  else
    echo "You must run this command from within a BLT-generated project."
    return 1
  fi
}

# BEGIN SNIPPET: Platform.sh CLI configuration
export PATH="$HOME/"'.platformsh/bin':"$PATH"
if [ -f "$HOME/"'.platformsh/shell-config.rc' ]; then . "$HOME/"'.platformsh/shell-config.rc'; fi # END SNIPPET
