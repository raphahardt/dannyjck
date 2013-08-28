<?php

Core::uses('SessionCommon', 'core/session');

Core::depends('Dbc');
Core::uses('Request', 'core/network'); // para saber o ip do visitante

class SessionDbc extends SessionCommon {
  
  static public function open($savePath, $session_name) {
    return true;
  }

  static public function close() {
    return true;
  }

  static public function read($sid) {
    
    $string = '';
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('select * from fm_session where sid = ?');
    $dbc->bind_param($sid);
    if ($dbc->execute()) {
      $row = $dbc->fetch_assoc();
      
      $string = self::_decryptData($row['sessao'], SESSION_NAME);
    }
    $dbc->free();
    
    return $string;
  }

  static public function write($sid, $data) {
    
    $req = new Request();
    
    $ip = $req->clientIp();
    $timestamp = time();
    $data = self::_encryptData($data, SESSION_NAME);
    
    // pega o id do usuario logado
    $user = $_SESSION[ SESSION_USER_NAME ];
    $uid = (is_object($user) && $user->id) ? $user->id : 0;
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('insert into fm_session (id,sid,ip,timestamp,sessao) values (?,?,?,?,?) on duplicate key update id=?, timestamp=?,sessao=?');
    // insert
    $dbc->bind_param($uid);
    $dbc->bind_param($sid);
    $dbc->bind_param($ip);
    $dbc->bind_param($timestamp);
    $dbc->bind_param($data);
    // update
    $dbc->bind_param($uid);
    $dbc->bind_param($timestamp);
    $dbc->bind_param($data);
    $success = $dbc->execute();

    $dbc->free();
    
    return $success;
    
  }

  static public function destroy($sid) {
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('delete from fm_session where sid = ?');
    $dbc->bind_param($sid);
    $success = $dbc->execute();

    $dbc->free();
    
    return $success;
    
  }

  static public function gc($lifetime) {
    
    $time = time() - $lifetime;
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('delete from fm_session where timestamp < ?');
    $dbc->bind_param($time);
    $success = $dbc->execute();

    $dbc->free();
    
    return $success;
    
  }
  
  static public function regenerate($old_sid) {
    
    session_regenerate_id(true);
    
    /*$new_sid = g_sessionid();
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('update fm_session set sid=? where sid = ?');
    $dbc->bind_param($new_sid);
    $dbc->bind_param($old_sid);
    $success = $dbc->execute();

    $dbc->free();
    
    $cookie_session = &self::$cookie;
    if ($success) {
      $cookie_session->set($new_sid);
    }
    
    return $success ? $new_sid : $old_sid;*/

  }
  
}
