<?php

Core::setup(); // carrega os autoloads

Core::import('Response', 'core/network');
Core::import('Request', 'core/network');
Core::import('Router', 'core/router');

Core::import('Cookie', 'core/cookie');
Core::import('Session', 'core/session');

Core::import('Controller', 'core/mvc/controller');


// configuracoes do app
include APP_PATH.DS.'cfg'.DS.'controllers.php';
include APP_PATH.DS.'cfg'.DS.'routes.php';
//include APP_PATH.DS.'cfg'.DS.'session.php';

// configurações do core (default)
include DJCK.DS.'cfg'.DS.'controllers.php';
include DJCK.DS.'cfg'.DS.'routes.php';
include DJCK.DS.'cfg'.DS.'session.php';