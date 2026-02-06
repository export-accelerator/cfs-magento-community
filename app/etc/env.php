<?php
return array (
  'remote_storage' => 
  array (
    'driver' => 'file',
  ),
  'backend' => 
  array (
    'frontName' => 'a6e60546c0_admin',
  ),
  'cache' => 
  array (
    'graphql' => 
    array (
      'id_salt' => 'gPvxaZIbmguO75JyTxtDsbUuEtVMYQBy',
    ),
    'frontend' => 
    array (
      'default' => 
      array (
        'id_prefix' => 'e02_',
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => '/var/run/redis-multi-a7bb3042.redis/redis.sock',
          'database' => '1',
          'port' => '0',
        ),
      ),
      'page_cache' => 
      array (
        'id_prefix' => 'e02_',
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => '/var/run/redis-multi-a7bb3042.redis/redis.sock',
          'database' => '0',
          'port' => '0',
        ),
      ),
    ),
    'allow_parallel_generation' => false,
  ),
  'config' => 
  array (
    'async' => 0,
  ),
  'queue' => 
  array (
    'consumers_wait_for_messages' => 1,
  ),
  'crypt' => 
  array (
    'key' => 'base64iKItou7gGfAuTvliSh4vC1wj67t3Nm2f6idXGdC2GvQ=',
  ),
  'db' => 
  array (
    'table_prefix' => '',
    'connection' => 
    array (
      'default' => 
      array (
        'host' => 'localhost',
        'dbname' => 'a7bb3042_e4f9ea',
        'username' => 'a7bb3042_e4f9ea',
        'password' => 'Remind77Defile58Circus26Crisis51',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
        'driver_options' => 
        array (
          1014 => false,
        ),
      ),
    ),
  ),
  'resource' => 
  array (
    'default_setup' => 
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'session' => 
  array (
    'save' => 'redis',
    'redis' => 
    array (
      'host' => '/var/run/redis-multi-a7bb3042.redis/redis.sock',
      'port' => '0',
      'database' => '2',
      'compression_library' => 'gzip',
    ),
  ),
  'lock' => 
  array (
    'provider' => 'db',
    'config' => 
    array (
      'prefix' => '13cf764ecd95725a62e1f80616b1f2ad',
    ),
  ),
  'directories' => 
  array (
    'document_root_is_pub' => true,
  ),
  'cache_types' => 
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'compiled_config' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'graphql_query_resolver_result' => 1,
    'full_page' => 1,
    'config_webservice' => 1,
    'translate' => 1,
  ),
  'downloadable_domains' => 
  array (
    0 => 'ada68b36bd.nxcli.io',
  ),
  'install' => 
  array (
    'date' => 'Mon, 12 Jan 2026 04:13:25 +0000',
  ),
);
