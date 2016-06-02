<?php

/**
 * Implements hook_drush_command().
 */
function current_alias_drush_command() {
  $items = array();

  $items['current-alias'] = array(
    'description' => "Prints the current site alias in use, if there is one.",
    // Give one or more example commandline usages for this command
    'examples' => array(
      'drush current-alias' => 'Print current alias.',
    ),
    'aliases' => array('ca'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL, // No bootstrap at all. Remove for full bootstrap of Drupal site.
  );

  return $items;
}

/**
 * Implementation of drush_hook_COMMAND().
 *
 * Returns current alias, currently implemented simply as the root.
 */
function drush_current_alias() {
  $root = drush_get_context('DRUSH_SELECTED_DRUPAL_ROOT');
  if (strpos($root, '/Users/jameswilliams/parrot/sites') === 0) {
    $root = substr($root, 34);
  }
  $uri = drush_get_context('DRUSH_SELECTED_URI');
  if ($uri === 'http://' . $root) {
    return '@' . $root;
  }
  return NULL;
}
