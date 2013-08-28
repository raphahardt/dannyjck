<?php

/**
 * Carrega a session pra ser usada pelo sistema
 */

function check_sid($sid) {
  $middle = substr($sid, 2, -2);
  $agent = substr($middle, 0, 32);
  $md5 = substr($middle, 32, 32);
  $ip = (double)substr($middle, 64);
  //list(,$agent,$md5,$ip,) = explode('-', $sid);
  
  // confere browser
  if (md5(env('HTTP_USER_AGENT')) !== $agent)
    return false;
  
  // confere ip
  if (ip2long(env('REMOTE_ADDR')) != $ip)
    return false;
  
  // confere md5
  if (preg_match('/[^0-9a-f]/', $md5))
    return false;
  
  // tudo ok, sid confere
  return true;
}

class Sess {
  static public $savePath = 'C:\\session';
  
  public function open($savePath, $session_name) {
    
    if (!isset(self::$savePath))
      self::$savePath = $savePath;
    self::$savePath .= DS;
    
    if (!is_dir(self::$savePath)) {
      mkdir(self::$savePath, 0777);
    }
    return true;
  }

  public function close() {
    return true;
  }

  public function read($sid) {
    return (string)@file_get_contents(self::$savePath.SESSION_NAME."_$sid");
  }

  public function write($sid, $data) {
    return file_put_contents(self::$savePath.SESSION_NAME."_$sid", $data) === false ? false : true;
  }

  public function destroy($sid) {
    $file = self::$savePath.SESSION_NAME."_$sid";
    if (file_exists($file)) {
      unlink($file);
    }
    return true;
  }

  public function gc($lifetime) {
    foreach (glob(self::$savePath .SESSION_NAME."_*") as $file) {
      if (filemtime($file) + $lifetime < time() && file_exists($file)) {
        unlink($file);
      }
    }
    return true;
  }
  
}

$s_cookie = new Cookie( SESSION_NAME, 
        ((SESSION_TIMEOUT > 0) ?
          time() + SESSION_TIMEOUT :
          0), 
        null, null, null, true );

$sid = $s_cookie->get() ? $s_cookie->get() : g_sessionid();

if (!check_sid($sid)) {
  $sid = g_sessionid();
  //throw new Exception ('Tentando roubar informações, né?');
}

$handler = new Sess();
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
    );

register_shutdown_function('session_write_close');

if (SESSION_TIMEOUT > 0)
  session_set_cookie_params(SESSION_TIMEOUT);
session_name(SESSION_NAME);

session_id($sid);

session_start();
//$_SESSION['regera'] = true;

if (!$_SESSION['bbb'])
  $_SESSION['bbb'] = g_token();

$_SESSION['teste'] = 'f|dfsd"fdsf';

if ($_SESSION['regera']) {
  
  $new_sid = g_sessionid();
  
  $data = $handler->read($sid);
  $handler->destroy($sid);
  $handler->write($new_sid, $data);
  
  session_id($new_sid);
  $s_cookie->set($new_sid);
  unset($_SESSION['regera']);
}

if (!$_SESSION['counter'])
  $_SESSION['counter'] = 0;
$_SESSION['counter'] += 1;

print_r($_SESSION);

session_write_close();


/*Core::depends('Session');
Core::depends('Cookie');

$cookie_session = new Cookie( SESSION_NAME, time() + SESSION_TIMEOUT );
$cookie_sid = $cookie_session->get();

// seta o id da session, se o cookie 
if(!empty($cookie_sid)) {
  Session::setId($cookie_sid);
  //echo 'AAAAA';
}

// cria a sessao se ela ainda nao existir
if(!Session::isStarted()) 
  Session::start();

// cria um token de seguranca
$token = $_SESSION[ SESSION_TOKEN_NAME ];
if( empty($token) ) {
  $_SESSION[ SESSION_TOKEN_NAME ] = g_token();
}

//seta o sid global da sessao
//$sid = Session::getId();

// regera a sessao se o usuario acabou de logar (motivos de seguranca)
//$_SESSION['logged'] = true;
if(isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
  $sid = Session::regenerate($sid);
  
  //$cookie_session->set($sid);
  
  // já entrou, zerar variavel temporaria
  $_SESSION['logged'] = NULL;
  unset($_SESSION['logged']);
}

define('DJCK_TOKEN', $token);

// clean mem
unset($cookie_session, $cookie_sid, $sid, $token);
/**/