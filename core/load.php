<?php

/**
 * Carrega as classes core principais e as configurações referentes a core e app.
 * Não mudar este arquivo (somente em caso de algum core for essencial carregar). 
 */

Core::setup(); // carrega os autoloads

// interface
Core::import('Response', 'core/network');
Core::import('Request', 'core/network');
Core::import('Router', 'core/router');

// database
Core::import('Dbc', 'core/database/dbc');
Core::import('SQLBase', 'core/database/sql');

// mvc
Core::import('Controller', 'core/mvc/controller');
Core::import('Model', 'core/mvc/model');
Core::import('ModelCollection', 'core/mvc/model');
Core::import('View', 'core/mvc/view');

// client comm
Core::import('Cookie', 'core/cookie');
Core::import('Session', 'core/session');

// logger
Core::import('Logger', 'core/logger');


// configuracoes do app
include APP_PATH.DS.'cfg'.DS.'controllers.php';
include APP_PATH.DS.'cfg'.DS.'routes.php';
//include APP_PATH.DS.'cfg'.DS.'session.php';

// configurações do core (default)
include DJCK.DS.'cfg'.DS.'controllers.php';
include DJCK.DS.'cfg'.DS.'routes.php';
include DJCK.DS.'cfg'.DS.'session.php';