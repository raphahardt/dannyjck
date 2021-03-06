<?php

define('START', microtime(true));

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

/**
 * Pasta absoluta raiz do sistema. Sempre resolve pelo sistema mais "alto"
 * Se você tiver um subsistema que lê as configurações do sistema pai, essa constante
 * sempre vai apontar pra pasta root do sistema pai.
 */
if (!defined('DJCK'))
  define('DJCK', dirname(dirname(__FILE__)));

/**
 * Pasta absoluta raiz do seu projeto. Sempre resolve pelo sistema mais "baixo" (atual).
 * Esta constante sempre irá retornar a pasta root do subsistema, caso haja sistema pai,
 * ou simplesmente será igual a DJCK
 */
if (!defined('ROOT'))
  define('ROOT', DJCK);

/**
 * Pasta absoluta da "app" do seu projeto. Irá tentar resolver pela ROOT em vez de DJCK.
 */
if (!defined('APP_PATH'))
  define('APP_PATH', ROOT.DS.'app');

/**
 * Pasta absolita do "core" do projeto
 */
if (!defined('CORE_PATH'))
  define('CORE_PATH', DJCK.DS.'core');

/**
 * Pasta absoluta dos "plugins" do projeto
 */
if (!defined('PLUGIN_PATH'))
  define('PLUGIN_PATH', DJCK.DS.'plugins');

/**
 * Pasta absoluta dos arquivos temporarios do projeto
 */
if (!defined('TEMP_PATH'))
  define('TEMP_PATH', DJCK.DS.'_tmp');

/**
 * Pasta absoluta dos arquivos publicos do projeto
 */
if (!defined('PUBLIC_PATH'))
  define('PUBLIC_PATH', DJCK.DS.'public');

define('OS', PHP_OS);
define('_DEV', $_SERVER['HTTP_HOST'] === 'localhost');


// funções basicas
include CORE_PATH.DS.'basics.php';

// CORE
include CORE_PATH.DS.'core'.DS.'Core.php';

// carrega primeiro as definicoes do app, depois do core
include APP_PATH.DS.'cfg'.DS.'defs.php';
include CORE_PATH.DS.'defs.php';

if (!defined('_DEFS_ONLY'))
  include CORE_PATH.DS.'load.php';