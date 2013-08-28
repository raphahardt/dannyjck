<?php

Core::uses('SessionCommon', 'core/session');

class SessionFile extends SessionCommon {
  
  static private $savePath = 'C:\\session';
  
  static public function open($savePath, $session_name) {
    
    if (!isset(self::$savePath))
      self::$savePath = $savePath;
    self::$savePath .= DS;
    
    if (!is_dir(self::$savePath)) {
      mkdir(self::$savePath, 0777);
    }
    return true;
  }

  static public function close() {
    return true;
  }

  static public function read($sid) {
    return self::_decryptData((string)@file_get_contents(self::$savePath.SESSION_NAME."_$sid"), SESSION_NAME);
  }

  static public function write($sid, $data) {
    return file_put_contents(self::$savePath.SESSION_NAME."_$sid", self::_encryptData($data, SESSION_NAME)) === false ? false : true;
  }

  static public function destroy($sid) {
    $file = self::$savePath.SESSION_NAME."_$sid";
    if (file_exists($file)) {
      unlink($file);
    }
    return true;
  }

  static public function gc($lifetime) {
    foreach (glob(self::$savePath .SESSION_NAME."_*") as $file) {
      if (filemtime($file) + $lifetime < time() && file_exists($file)) {
        unlink($file);
      }
    }
    return true;
  }
  
  static public function regenerate($old_sid) {
    $new_sid = self::generateId();
    
    $data = self::read($old_sid);
    self::destroy($old_sid);
    $success = self::write($new_sid, $data);
    
    $cookie_session = &self::$cookie;
    if ($success)
      $cookie_session->set($new_sid);
    
    return $success ? $new_sid : $old_sid;
  }
  
}
