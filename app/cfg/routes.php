<?php

/**
 * Registra todas as rotas utilizadas pelo app.
 */

$Router = new Router();

/**
 * Login
 */
$Router->map('/login', array('LoginController' => null), 'login');
$Router->map('/login/esqueci', 'login#esqueci');
$Router->map('/login/auth', 'login#auth');
$Router->map('/logout', 'login#logout');

$Router->map('/imagem.jpg', 'home#favicon');


