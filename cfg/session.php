<?php

/**
 * Carrega a session pra ser usada pelo sistema
 */

if (!class_exists('Session', false)) {
  // se a classe não existir, criar uma nova classe
  Core::uses('SessionFile', 'core/session');
  
  if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
    // php novo: usa alias de classe, que é melhor para este propósito
    class_alias('SessionFile', 'Session');
  } else {
    // ..senão, usar extends para resolver, mas solução não é elegante
    class Session extends SessionFile {};
  }
}

if (!isset($Session)) {
  $Session = new Session();
}

// token
if (!$_SESSION[SESSION_TOKEN_NAME])
  $_SESSION[SESSION_TOKEN_NAME] = g_token();

// se usuario logou, regerar session
if ($_SESSION['logged']) {
  $Session->regenerateId();
  unset($_SESSION['logged']);
}

/**
 * Token de segurança da sessão.
 * Formulários com informações sensíveis devem trocar informações passando 
 * junto o token e conferindo com o da sessão.
 */
define('TOKEN', $_SESSION[SESSION_TOKEN_NAME]);