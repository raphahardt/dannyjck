<?php

define('START', microtime(true));

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

/**
 * Pasta absoluta raiz do seu projeto.
 */
if (!defined('DJCK'))
  define('DJCK', dirname(dirname(__FILE__)));

/**
 * Pasta absoluta da "app" do seu projeto
 */
if (!defined('APP_PATH'))
  define('APP_PATH', DJCK.DS.'app');

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

define('OS', PHP_OS);

date_default_timezone_set('America/Sao_Paulo');

// funções basicas
include CORE_PATH.DS.'basics.php';

// CORE
include CORE_PATH.DS.'core'.DS.'Core.php';

// carrega primeiro as definicoes do app, depois do core
include APP_PATH.DS.'cfg'.DS.'defs.php';
include CORE_PATH.DS.'defs.php';

if (!defined('DEFS_ONLY'))
  include CORE_PATH.DS.'load.php';