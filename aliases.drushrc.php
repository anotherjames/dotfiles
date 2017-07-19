<?php

// Set this to TRUE to be able to use xdebug with local drush files. Or FALSE to
// run inside parrot.
$run_locally = FALSE;

$aliases = array();
$dir_handle = new DirectoryIterator(drush_server_home() . '/parrot/sites');
$global_drush_match = NULL;
$global_drush_version = explode('.', '8.1.2');
$find_drush_script = array();
$drushes = array();
while ($dir_handle->valid()) {
  if ($dir_handle->isDir() && !$dir_handle->isDot()) {
    // Does this subdirectory contain a Drupal site?
    $prefix = '';
    if (is_dir($dir_handle->getPathname() . '/webroot')) {
      $prefix = '/webroot';
    }

    if (file_exists($dir_handle->getPathname() . $prefix . '/sites/default/default.settings.php') || file_exists($dir_handle->getPathname() . $prefix . '/sites/default/settings.php')) {
      $basename = $dir_handle->getBasename();
      $aliases[$basename] = array(
        'uri' => 'https://' . $basename,
        // A local version of drush for the site will handle being in the
        // webroot subdirectory. A symlink from /vagrant_sites pointing to the
        // parrot sites directory (i.e. both on the host machine) can be used.
        'root' => '/vagrant_sites/' . $basename . $prefix,
      );

      if ($run_locally) {
        // Still need to get the right version of PHP, but use phpenv if running
        // locally. Unset phpenv's environment variable so that running
        // `phpenv which php` returns the appropriate php script for this site
        // (otherwise it just returns the one set by the environment variable
        // for this php request).
        $phpenv_ev = getenv('PHPENV_VERSION');
        if ($phpenv_ev !== FALSE) {
          // This unsets the current PHPENV_VERSION environment variable.
          putenv('PHPENV_VERSION');
        }
        drush_shell_cd_and_exec($dir_handle->getPathname(), 'phpenv which php');
        $php_env_output = drush_shell_exec_output();
        if ($phpenv_ev !== FALSE) {
          putenv('PHPENV_VERSION=' . $phpenv_ev);
        }
        if (isset($php_env_output[2]) && file_exists($php_env_output[2])) {
          $aliases[$basename]['php'] = $php_env_output[2];
          // Without this local option, commands are called twice. Reported at
          // https://github.com/drush-ops/drush/issues/1870.
          // @TODO Any that can 'handle-remote-commands' (e.g. uli) will still
          // run twice.
          $aliases[$basename]['local'] = TRUE;
          // However, the local flag stops this alias file from being loaded &
          // used when an alias is in use, which then throws an error because
          // the alias cannot be found. Setting a remote-host ensures a
          // redispatch with the right details for the alias.
          $aliases[$basename]['remote-host'] = 'localhost';
        }
      }
      else {
        // Parrot only supports two php versions.
        if (file_exists($dir_handle->getPathname() . '/.parrot-php7')) {
          $aliases[$basename]['php'] = '/usr/bin/php7.0';
        }
        else {
          $aliases[$basename]['php'] = '/usr/bin/php5.6';
        }

        $aliases[$basename]['remote-host'] = $basename;
        $aliases[$basename]['remote-user'] = 'vagrant';
        // Without this local option, commands are called twice. Reported at
        // https://github.com/drush-ops/drush/issues/1870.
        $aliases[$basename]['local'] = TRUE;
        $aliases[$basename]['ssh-options'] = '-o PasswordAuthentication=no -p 2222 -i ' . drush_server_home() . '/parrot/.vagrant/machines/default/virtualbox/private_key';
      }

      // @TODO Use the right version of drush when running locally too, if
      // possible. However, at the moment, setting the %drush-script bit stops
      // the alias file actually being loaded, so the alias cannot be found.
      // This is because a redispatch will happen to use the specified drush
      // script at an early stage, which will have the 'local' flag set as
      // above, before the actually using the remote-host bit.
      if (!$run_locally) {
        if (file_exists($dir_handle->getPathname() . '/vendor/drush/drush/drush.launcher')) {
          $aliases[$basename]['path-aliases']['%drush-script'] = '/vagrant_sites/' . $basename . '/vendor/drush/drush/drush.launcher';
          $drush_info_file = '/vagrant_sites/' . $basename . '/vendor/drush/drush/drush.info';
          if (!$global_drush_match && file_exists($drush_info_file)) {
            $drush_info = parse_ini_file($drush_info_file);
            if (isset($drush_info['drush_version'])) {
              if ($drush_info['drush_version'] == $global_drush_version) {
                $global_drush_match = $basename;
              }
              else {
                $drushes[$basename] = explode('.', $drush_info['drush_version']);
              }
            }
          }
        }
        // The global drush script (at /usr/local/bin/drush) does not seem to
        // respect the php version. So try to use a drush launcher from
        // elsewhere, ideally matching the global version.
        elseif (!empty($aliases[$basename]['php'])) {
          $find_drush_script[] = $basename;
        }
      }
    }
  }
  $dir_handle->next();
}

if ($find_drush_script) {
  if (!$global_drush_match) {
    if ($drushes) {
      $candidates = $drushes;
      for ($i = 0; $i < 3; $i++) {
        foreach ($candidates as $basename => $candidate) {
          if ($candidate[$i] != $global_drush_version[$i]) {
            unset($candidates[$basename]);
          }
        }
        if ($count = count($candidates)) {
          reset($candidates);
          if ($count === 1) {
            $global_drush_match = key($candidates);
            break;
          }
          else {
            // Narrow the options down.
            $drushes = $candidates;
            continue;
          }
        }
        else {
          // @TODO An exact match was not found. Use the closest, preferably
          // newer, version instead.
        }
      }
    }
  }
  if ($global_drush_match) {
    foreach ($find_drush_script as $basename) {
      $aliases[$basename]['path-aliases']['%drush-script'] = $aliases[$global_drush_match]['path-aliases']['%drush-script'];
    }
  }
  else {
    // Items in $find_drush_script will not necessarily run under the correct
    // PHP version.
  }
}

// Get all site aliases
$all = array();
foreach ($aliases as $name => $definition) {
  $all[] = '@' . $name;
}

// 'All' alias group
$aliases['all'] = array(
  'site-list' => $all,
);
