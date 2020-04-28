<?php

$advanced = TRUE;
$debugging = extension_loaded('xdebug') && array_key_exists('XDEBUG_CONFIG', $_SERVER);
// Set this to TRUE to be able to use xdebug with local drush files. Or FALSE to
// run inside parrot.
$run_locally = $debugging && $advanced;

$aliases = array();
$home_dir = drush_server_home();
$dir_handle = new DirectoryIterator($home_dir . '/parrot/sites');
$global_drush_match = NULL;
$global_drush_version = explode('.', '8.1.2');
$find_drush_script = array();
$drushes = array();
$include_paths = array();
$command_can_run_remotely = FALSE;
$phpenv_ev = getenv('PHPENV_VERSION');
$remote_username = 'vagrant';

if ($advanced && $run_locally) {
  // To get the right version of PHP, unset phpenv's environment variable so
  // that running `phpenv which php` returns the appropriate php script for this
  // site (otherwise it just returns the one set by the environment variable for
  // this php request). It will be restored after setting up all the aliases.
  if ($phpenv_ev !== FALSE) {
    // This unsets the current PHPENV_VERSION environment variable.
    putenv('PHPENV_VERSION');
  }

  // @TODO Calling this so early will mean the command is parsed earlier than is
  // ideal. The commands static cache is reset at the end of this file to allow
  // sites to check/parse commands again, but I'm not sure this is ideal.
  $command = drush_parse_command();

  // Any commands that can 'handle-remote-commands' (e.g. uli) can be
  // run as they are. Otherwise, tweak some options (see comments).
  if (is_array($command) && !empty($command['handle-remote-commands'])) {
    $command_can_run_remotely = TRUE;
  }
  // When running locally, _drush_find_commandfiles_drush() will not look for
  // commands in all the normal places. Pass include paths along for dispatches.
  else {
    // User commands, specified by 'include' option
    $include = drush_get_context('DRUSH_INCLUDE', array());
    foreach ($include as $path) {
      if (is_dir($path)) {
        $include_paths[] = $path;
      }
    }

    // System commands, residing in $SHARE_PREFIX/share/drush/commands
    $share_path = drush_get_context('DRUSH_SITE_WIDE_COMMANDFILES');
    if (is_dir($share_path)) {
      $include_paths[] = $share_path;
    }

    // User commands, residing in ~/.drush
    $per_user_config_dir = drush_get_context('DRUSH_PER_USER_CONFIGURATION');
    if (!empty($per_user_config_dir)) {
      $include_paths[] = $per_user_config_dir;
    }
  }
}

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

      if ($advanced) {
        if ($run_locally) {
          drush_shell_cd_and_exec($dir_handle->getPathname(), 'phpenv which php');
          $php_env_output = drush_shell_exec_output();
          if (isset($php_env_output[2]) && file_exists($php_env_output[2])) {
            $aliases[$basename]['php'] = $php_env_output[2];

            // Any commands that can 'handle-remote-commands' (e.g. uli) can be
            // run as they are. Otherwise, tweak some options (see comments).
            if (!$command_can_run_remotely) {
              // Without this local option, commands are called twice. Reported
              // at https://github.com/drush-ops/drush/issues/1870.
              $aliases[$basename]['local'] = TRUE;
              // However, the local flag stops this alias file from being loaded
              // & used when an alias is in use, which then throws an error
              // because the alias cannot be found. Setting a remote-host
              // ensures a redispatch with the right details for the alias.
              $aliases[$basename]['remote-host'] = 'localhost';

              if ($include_paths) {
                $aliases[$basename]['include'] = $include_paths;
              }
            }
          }
        }
        else {
          // Assume newer version of parrot that can run PHP 7.0 or 7.1.
          if (file_exists($dir_handle->getPathname() . '/.parrot-php7.0')) {
            $aliases[$basename]['php'] = '/usr/bin/php7.0';
          }
          elseif (file_exists($dir_handle->getPathname() . '/.parrot-php7') || file_exists($dir_handle->getPathname() . '/.parrot-php7.1')) {
            $aliases[$basename]['php'] = '/usr/bin/php7.1';
          }
          else {
            $aliases[$basename]['php'] = '/usr/bin/php5.6';
          }

          $aliases[$basename]['remote-host'] = $basename;
          $aliases[$basename]['remote-user'] = $remote_username;
          // Without this local option, commands are called twice. Reported at
          // https://github.com/drush-ops/drush/issues/1870.
          $aliases[$basename]['local'] = TRUE;
          $aliases[$basename]['ssh-options'] = '-o PasswordAuthentication=no -p 2222 -i ' . $home_dir . '/parrot/.vagrant/machines/default/virtualbox/private_key';

          // Since local is set to TRUE, we do need to include command locations
          // that would not otherwise be included. This is the equivalent of
          // drush_get_context('DRUSH_PER_USER_CONFIGURATION') which is included
          // when running locally. We could include the equivalent of
          // drush_get_context('DRUSH_SITE_WIDE_COMMANDFILES') too, but there's
          // nothing there anyway (AFAIK).
          $aliases[$basename]['include'] = array('/home/' . $remote_username . '/.drush/');

          // Use the right version of drush when running inside parrot.
          // @TODO Use the right version of drush when running locally too, if
          // possible. However, at the moment, setting the %drush-script bit
          // stops the alias file actually being loaded, so the alias cannot be
          // found. This is because a redispatch will happen to use the
          // specified drush script at an early stage, which will have the
          // 'local' flag set as above, before the actually using the
          // remote-host bit.
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
  }
  $dir_handle->next();
}

if ($advanced && $run_locally) {
  if ($phpenv_ev !== FALSE) {
    putenv('PHPENV_VERSION=' . $phpenv_ev);
  }
}

// In order for Drush to respect the passed PHP version, we have to directly
// call a drush.launcher file rather than parrot's global drush.
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
      }

      // An exact match was still not found. Use the closest, preferably newer,
      // version that has a launcher file instead.
      if (!$global_drush_match) {
        $patch_versions = array(
          $global_drush_version[2] => 'global',
        );
        foreach ($drushes as $basename => $candidate) {
          $patch_version = $candidate[2];
          $patch_versions[$patch_version] = $basename;
        }
        ksort($patch_versions, SORT_NUMERIC);

        $patch_versions_keys = array_keys($patch_versions);
        $patch_versions_basenames = array_values($patch_versions);

        $pos = array_search('global', $patch_versions_basenames);
        // If there are newer versions of drush available, use the oldest of the
        // newer ones (i.e. closest to the global drush version).
        if ($pos < (count($patch_versions) - 1)) {
          $global_drush_match = $patch_versions_basenames[$pos+1];
        }
        // If there are only older versions of drush available, use the newest.
        else {
          $global_drush_match = $patch_versions_basenames[$pos-1];
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

if ($advanced && $run_locally) {
  drush_get_commands(TRUE);
}
