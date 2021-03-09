<?php

/**
 * @file
 * Writes drush alias files for new Drupal sites.
 */

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Dumper;

$home_dir = getenv('HOME');
$alias_dir = $home_dir . '/.drush/sites';
if (is_dir($alias_dir)) {
  $dispatcher = new EventDispatcher();
  /** @var \Silly\Application $app */
  $app->setDispatcher($dispatcher);
  $dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleEvent $event) use ($home_dir, $alias_dir) {
    $command = $event->getCommand();
    if ($command->getName() === 'link') {
      $cwd = getcwd();
      $input = $event->getInput();
      $domain = $input->getArgument('name') ?: basename($cwd);

      // Check this is a Drupal site (at least as far as Valet understands).
      $drivers_dir = $home_dir . '/.composer/vendor/weprovide/valet-plus/cli/drivers/';
      /** @noinspection PhpIncludeInspection */
      if ($included = include_once $drivers_dir . 'ValetDriver.php') {
        /** @noinspection PhpIncludeInspection */
        if (include_once $drivers_dir . 'DrupalValetDriver.php') {
          $driverInstance = new DrupalValetDriver();
          if ($driverInstance->serves($cwd, $domain, '')) {
            // Now figure out adding a drush alias!
            $alias_name = str_replace('.', '', $domain);
            $alias_filename = $alias_dir . '/' . $alias_name . '.site.yml';
            if (!file_exists($alias_filename)) {
              $protocol = $input->getOption('secure') ? 'https' : 'http';

              /** @noinspection PhpUndefinedMethodInspection */
              $data = array(
                '*' => array(
                  'root' => $cwd,
                  /** @var \Valet\Configuration Configuration */
                  'uri' => $protocol . '://' . $domain . '.' . Configuration::read()['domain'],
                )
              );

              try {
                // Set the indentation to 2 to match Drupal's coding standards.
                $yaml = new Dumper(2);
                $encoded = $yaml->dump($data, PHP_INT_MAX);
                file_put_contents($alias_filename, $encoded);
              }
              catch (\Exception $e) {
                // Skip this alias.
              }
            }
          }
        }
      }
    }
  });
}
