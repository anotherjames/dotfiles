<?php

/**
 * Implements hook_drush_command().
 */
function cachereset_drush_command() {
  $items = array();

  $items['cache-reset'] = array(
    'description' => "Rebuilds or totally clears the cache, depending on the Drupal core version.",
    // Give one or more example commandline usages for this command
    'examples' => array(
      'drush cache-reset' => 'Resets the cache.',
    ),
    'aliases' => array('cr'),
    // Bootstrap to DRUSH_BOOTSTAP_DRUPAL_SITE to pick the correct site.
    // Further bootstrap is done by the command script.
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_SITE,
  );

  return $items;
}

/**
 * Implementation of drush_hook_COMMAND().
 *
 * Returns current alias, currently implemented simply as the root.
 */
function drush_cachereset_cache_reset() {
  if (drush_drupal_major_version() >= 8) {
    drush_invoke_process('@self', 'cache-rebuild');
  }
  else {
    drush_invoke_process('@self', 'cache-clear', array('all'));
  }
}
