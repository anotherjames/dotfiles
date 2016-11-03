<?php

$aliases = array();
$dir_handle = new DirectoryIterator(drush_server_home() . '/parrot/sites');
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
        'uri' => 'http://' . $basename,
        // A local version of drush for the site will handle being in the
        // webroot subdirectory.
        'root' => '/vagrant_sites/' . $basename . '/' . $prefix,
      );
      if (file_exists($dir_handle->getPathname() . '/.php-version')) {
        $php_version = trim(file_get_contents($dir_handle->getPathname() . '/.php-version'));
        $parts = array_pad(explode('.', $php_version, 3), 3, 0);
        $aliases[$basename]['php'] = '/usr/bin/php' . $parts[0] . '.' . $parts[1];
      }
      else {
        $aliases[$basename]['php'] = '/usr/bin/php';
      }
      if (file_exists($dir_handle->getPathname() . $prefix . '/sites/default/default.services.yml') || file_exists($dir_handle->getPathname() . $prefix . '/sites/default/services.yml')) {
        $aliases[$basename]['remote-host'] = 'localhost';
        $aliases[$basename]['remote-user'] = 'vagrant';
        $aliases[$basename]['ssh-options'] = '-o PasswordAuthentication=no -p 2222 -i ' . drush_server_home() . '/parrot/.vagrant/machines/default/virtualbox/private_key';
      }
    }
  }
  $dir_handle->next();
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
