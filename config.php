<?php

DJKApp::$Loader->setup();
DJKApp::$Loader->register('HomeController', 'HomeController', 'controller');
DJKApp::$Loader->register('View', 'view/View', 'core');
DJKApp::$Loader->dump();

DJKApp::$Router->connect('home', array('controller' => 'HomeController'));
DJKApp::$Router->connect('', array('type' => 'link', 'path' => 'home'));
DJKApp::$Router->connect('root', array('type' => 'link', 'path' => 'home'));

DJKApp::$Router->connect('error', array('controller' => 'ErrorController'));

/*DJKApp::$router->connect(array('#url' => 'login', '#controller' => 'LoginController'));
DJKApp::$router->connect(array('#url' => 'logout', '#type' => 'link', '#path' => 'login', '#action' => 'logout'));

DJKApp::$router->connect(array('#url' => 'area-autor', '#controller' => 'AreaAutorController'));
DJKApp::$router->connect(array('#url' => 'area-autor/series', '#type' => 'link', '#path' => 'area-autor', '#action' => 'open_series'));
DJKApp::$router->connect(array('#url' => 'area-autor/series/:serie', '#type' => 'link', '#path' => 'series', '#action' => 'open_serie', '#params' => array(':serie' => '/[a-z0-9_-]+/')));
DJKApp::$router->connect(array('#url' => 'area-autor/series/:serie/:capt', '#type' => 'link', '#path' => 'series', '#action' => 'open_capt', '#params' => array(':serie' => '/[a-z0-9_-]+/', ':capt' => '/[a-z0-9_-]+/')));
DJKApp::$router->connect(array('#url' => 'area-autor/edit-serie/:id', '#type' => 'link', '#path' => 'series', '#action' => 'edit_serie', '#params' => array(':id' => '/[a-z0-9_-]+/')));
DJKApp::$router->connect(array('#url' => 'area-autor/edit-capt/:id', '#type' => 'link', '#path' => 'series', '#action' => 'edit_capt', '#params' => array(':id' => '/[a-z0-9_-]+/')));
DJKApp::$router->connect(array('#url' => 'area-autor/edit-page/:id', '#type' => 'link', '#path' => 'series', '#action' => 'edit_pagina', '#params' => array(':id' => '/[a-z0-9_-]+/')));

DJKApp::$router->connect(array('#url' => 'a-revista', '#controller' => 'QuemSomosController'));
DJKApp::$router->connect(array('#url' => 'contato', '#controller' => 'ContatoController'));
DJKApp::$router->connect(array('#url' => 'parceiros', '#controller' => 'ParceirosController'));
DJKApp::$router->connect(array('#url' => 'extras', '#controller' => 'ExtrasController'));
DJKApp::$router->connect(array('#url' => 'publique', '#controller' => 'PubliqueController'));
DJKApp::$router->connect(array('#url' => 'anuncie', '#controller' => 'AnuncieController'));
DJKApp::$router->connect(array('#url' => 'trabalhe', '#controller' => 'TrabalheController'));
DJKApp::$router->connect(array('#url' => 'politica-privacidade', '#controller' => 'PoliticaPrivacidadeController'));
DJKApp::$router->connect(array('#url' => 'privacidade', '#type' => 'link', '#path' => 'politica-privacidade' ));

DJKApp::$router->connect(array('#url' => 'api', '#controller' => 'APIController'));
DJKApp::$router->connect(array('#url' => 'api/votante/:id', '#type' => 'link', '#path' => 'api', '#action' => 'votante', '#params' => array(':id' => '/[a-z0-9_-]+/')));
DJKApp::$router->connect(array('#url' => 'connect/facebook', '#type' => 'link', '#path' => 'api', '#action' => 'connect_facebook'));
DJKApp::$router->connect(array('#url' => 'connect/twitter', '#type' => 'link', '#path' => 'api', '#action' => 'connect_twitter'));
DJKApp::$router->connect(array('#url' => 'connect/google', '#type' => 'link', '#path' => 'api', '#action' => 'connect_google'));

DJKApp::$router->connect(array('#url' => 'edicoes', '#type' => 'link', '#path' => 'leitor', '#action' => 'index'));

DJKApp::$router->connect(array('#url' => 'series', '#controller' => 'SeriesController'));
DJKApp::$router->connect(array('#url' => 'series/:serie', '#type' => 'link', '#path' => 'series', '#action' => 'open', '#params' => array(':serie' => '/[a-z0-9_-]+/')));

DJKApp::$router->connect(array('#url' => 'noticias', '#controller' => 'NoticiasController'));
DJKApp::$router->connect(array('#url' => 'noticias/:key', '#type' => 'link', '#path' => 'noticias', '#action' => 'open', '#params' => array(':key' => '/[a-z0-9_-]+/')));

DJKApp::$router->connect(array('#url' => 'autores', '#controller' => 'AutoresController'));
DJKApp::$router->connect(array('#url' => 'autores/:autor', '#type' => 'link', '#path' => 'autores', '#action' => 'open', '#params' => array(':autor' => '/[a-z0-9_-]+/')));

DJKApp::$router->connect(array('#url' => 'leitor', '#controller' => 'LeitorController'));
DJKApp::$router->connect(array('#url' => 'leitor/edicoes/:year/:edition', '#type' => 'link', '#path' => 'leitor', '#action' => 'open_edition', '#params' => array(':edition' => '/[a-z0-9]+/', ':year' => '/[a-z0-9]+/')));
DJKApp::$router->connect(array('#url' => 'leitor/edicoes/:year/:edition/vote', '#type' => 'link', '#path' => 'leitor', '#action' => 'vote', '#params' => array(':edition' => '/[a-z0-9]+/', ':year' => '/[a-z0-9]+/')));
DJKApp::$router->connect(array('#url' => 'leitor/edicoes/:year/:edition/page/:page', '#type' => 'link', '#path' => 'leitor', '#action' => 'open_edition', '#params' => array(':edition' => '/[a-z0-9]+/', ':year' => '/[a-z0-9]+/', ':page' => '/[0-9]+/')));
DJKApp::$router->connect(array('#url' => 'leitor/edicoes/:year/:edition/download/:which', '#type' => 'link', '#path' => 'leitor', '#action' => 'download', '#params' => array(':edition' => '/[a-z0-9]+/', ':year' => '/[a-z0-9]+/', ':which' => '/[a-z]+/')));
DJKApp::$router->connect(array('#url' => 'leitor/login', '#type' => 'link', '#path' => 'leitor', '#action' => 'login'));
DJKApp::$router->connect(array('#url' => 'leitor/logout', '#type' => 'link', '#path' => 'leitor', '#action' => 'logout'));

DJKApp::$router->connect(array('#url' => 'leitor/serie/:serie', '#type' => 'link', '#path' => 'leitor', '#action' => 'open_series', '#params' => array(':serie' => '/[a-z0-9]+/')));
DJKApp::$router->connect(array('#url' => 'leitor/serie/:serie/page/:page', '#type' => 'link', '#path' => 'leitor', '#action' => 'open_series', '#params' => array(':serie' => '/[a-z0-9]+/', ':page' => '/[0-9]+/')));
 */