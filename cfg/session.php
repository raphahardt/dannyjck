<?php

/**
 * Carrega a session pra ser usada pelo sistema
 */

Core::depends('Session');
Core::depends('Cookie');

$cookie_session = new Cookie( SESSION_NAME, time() + SESSION_TIMEOUT );
$cookie_sid = $cookie_session->get();

// seta o id da session, se o cookie 
if(!empty($cookie_sid)) 
  Session::setId($cookie_sid);

// cria a sessao se ela ainda nao existir
if(!Session::isStarted()) 
  Session::start();

// cria um token de seguranca
$token = $_SESSION[ SESSION_TOKEN_NAME ];
if( empty($token) ) {
  $_SESSION[ SESSION_TOKEN_NAME ] = g_token();
}

//seta o sid global da sessao
$sid = Session::getId();

// regera a sessao se o usuario acabou de logar (motivos de seguranca)
if(isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
  $sid = Session::regenerate($sid);
  
  $cookie_session->set($sid);
  
  // já entrou, zerar variavel temporaria
  $_SESSION['logged'] = NULL;
  unset($_SESSION['logged']);
}

define('DJCK_TOKEN', $token);

// clean mem
unset($cookie_session, $cookie_sid, $sid, $token);