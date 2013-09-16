<?php

/**
 * Carrega as classes core principais e as configurações referentes a core e app.
 * Não mudar este arquivo (somente em caso de algum core for essencial carregar). 
 */

Core::setup(); // carrega os autoloads

// interface
Core::import('Response', 'core/network');
Core::import('Request', 'core/network');

// database
Core::import('DbcConfig', 'core/database/dbc');
Core::import('Dbc', 'core/database/dbc');
Core::import('SQLBase', 'core/database/sql');

// mvc
Core::import('Controller', 'core/mvc/controller');
Core::import('Model', 'core/mvc/model');
Core::import('ModelCollection', 'core/mvc/model');
Core::import('View', 'core/mvc/view');

// router
Core::import('Router', 'core/router');

// menu
//Core::import('Menu', 'core/menu');

// client comm
Core::import('Cookie', 'core/cookie');
//Core::import('Session', 'core/session');

// logger
Core::import('Logger', 'core/logger');


$cfgfiles = array(
    'connections.php',
    'controllers.php',
    'routes.php',
    'session.php',
);
foreach ($cfgfiles as $f) {
  // configurações do app
  if (is_file(APP_PATH.DS.'cfg'.DS.$f))
    include APP_PATH.DS.'cfg'.DS.$f;
  
  // configurações do core (default)
  if (is_file(DJCK.DS.'cfg'.DS.$f))
    include DJCK.DS.'cfg'.DS.$f;
}

unset($cfgfiles);