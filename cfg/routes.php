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
$Router->map('home', array('HomeController' => null));

/**
 * Alias "vazio" para home (ex: ex.com/ )
 */
$Router->map('', 'home');

/**
 * Rota especial para fazer o cache do favicon via php
 */
$Router->map('favicon.ico', 'home#favicon');

/**
 * Rota padrão para página de erro
 */
$Router->map('error', array('ErrorController' => null));
