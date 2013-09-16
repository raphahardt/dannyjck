<?php

/**
 * Registra todas as rotas utilizadas pelo projeto.
 * 
 * Para registrar as do app, utilize o APP/cfg/routes.php
 */

if (!isset($Router)) {
  $Router = new Router();
}

/**
 * Rota padrão para página inicial (ex: ex.com/home )
 */
$Router->map('/', array('HomeController' => null), 'home');

/**
 * Rota especial para fazer o cache do favicon via php
 */
$Router->map('/favicon.ico', 'home#favicon');

/**
 * Rota padrão para página de erro
 */
$Router->map('/error', array('ErrorController' => null), 'error');

/**
 * Rota padrão para conteúdos estáticos (imagens)
 */
$Router->map('/static/[*:file]', array('StaticContentController' => null), 'statics');

/*$Router->map('static/:dir/:file', array('StaticContentController' => null), 
        array( 'filters' => array( ':dir'=>'.*',
                                   ':file'=>'\.(jpg|gif|png)$') ));*/

/**
 * Rota que redireciona pra um arquivo, caso a rota seja qualquer coisa
 */
$Router->map('[*:file]', 'statics#file');