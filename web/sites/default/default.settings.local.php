<?php

$databases['default']['default'] = [
  'database' => 'drupal8_ada',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];

$settings['hash_salt'] = 'tze8UND4ZX6fFD83mTRKMhNhLrDVarjCUPgZWghxtJrJNLlpkvl_sVkPAFTeF4I1ev_E3lmJTw';

/* Useful settings for development: */
if (isset($config_directories[CONFIG_SYNC_DIRECTORY])) {
  unset($config_directories[CONFIG_SYNC_DIRECTORY]);
}

//$settings['file_temp_path'] = '/tmp';
$settings['file_temp_path'] = '/Applications/MAMP/tmp/php';

$settings['file_private_path'] = '../private/default';

$settings['trusted_host_patterns'] = [
  'alianzadeamoraqp',
];

// Verbose error logging including backtrace.
$config['system.logging']['error_level'] = 'verbose';

// Reduce caching.
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

//$settings['cache']['bins']['page'] = 'cache.backend.null';
//$settings['cache']['bins']['render'] = 'cache.backend.null';
//$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

// Output twig template file names.
$local_services = DRUPAL_ROOT . '/sites/services.local.yml';
if (file_exists($local_services)) {
  $settings['container_yamls'][] = $local_services;
}
