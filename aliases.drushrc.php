<?php

$aliases = array();
$dir_handle = new DirectoryIterator('/Users/jameswilliams/parrot/sites');
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
        'root' => $dir_handle->getPathname(),
      );
      if (file_exists($dir_handle->getPathname() . '/.php-version')) {
        $php_version = trim(file_get_contents($dir_handle->getPathname() . '/.php-version'));
        $aliases[$basename]['php'] = '/opt/boxen/phpenv/versions/' . $php_version . '/bin/php';
      }
      if (file_exists($dir_handle->getPathname() . $prefix . '/sites/default/default.services.yml') || file_exists($dir_handle->getPathname() . $prefix . '/sites/default/services.yml')) {
        $aliases[$basename]['path-aliases'] = array('%drush-script' => 'drush8');
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
