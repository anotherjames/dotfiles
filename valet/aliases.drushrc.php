<?php

$aliases = array();
$home_dir = drush_server_home();
$sites_subdir = '/sites';
$candidate_subdirs = array(
  '/webroot',
  '/web',
  '/docroot',
);
$dir_handle = new DirectoryIterator($home_dir . $sites_subdir);

while ($dir_handle->valid()) {
  if ($dir_handle->isDir() && !$dir_handle->isDot()) {
    // Does this subdirectory contain a Drupal site?
    $prefix = '';
    foreach ($candidate_subdirs as $subdir) {
      if (is_dir($dir_handle->getPathname() . $subdir)) {
        $prefix = $subdir;
        break;
      }
    }

    if (file_exists($dir_handle->getPathname() . $prefix . '/sites/default/default.settings.php') || file_exists($dir_handle->getPathname() . $prefix . '/sites/default/settings.php')) {
      $basename = $dir_handle->getBasename();
      $aliases[$basename] = array(
        'uri' => 'https://' . $basename . '.test',
        'root' => $home_dir . $sites_subdir . '/' . $basename . $prefix,
      );
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
