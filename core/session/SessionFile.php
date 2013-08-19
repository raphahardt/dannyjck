<?php

Core::depends('Cookie');

class SessionFileException extends Exception {};

class SessionFile implements ArrayAccess, Countable {
  
  private $reserved_names = array(
      SESSION_USER_NAME => 1, SESSION_TOKEN_NAME => 1, 'id' => 1, 'logged' => 1
  );
  
  static private $started = false;
  
  static private $savePath;
  
  static public function isStarted() {
    return self::$started;
  }
  
  static public function start() {
    
    session_set_save_handler(
      array('Session', 'open'), 
      array('Session', 'close'), 
      array('Session', 'read'), 
      array('Session', 'write'), 
      array('Session', 'destroy'), 
      array('Session', 'gc')
    );
    
    register_shutdown_function('session_write_close');
    
    if (SESSION_TIMEOUT > 0)
      session_set_cookie_params(SESSION_TIMEOUT);
    session_name(SESSION_NAME);
    
    try {
      
      $sid = session_id();

      if (empty($sid)) { //soh invoca na primeira requisicao (quando a session nao foi criada ainda)
        self::generateId();
      }
      
      session_start();
      
      // refresha o timeout da session na mão (medida de segurança da session pro cookie)
      if (SESSION_TIMEOUT > 0) {
        $cookie_session = new Cookie( SESSION_NAME, time() + SESSION_TIMEOUT );
        if($cookie_session->get()) 
          $cookie_session->set( $sid );
      }
      
      self::$started = true;
      // BUG:
      // quando session_start() é chamada, ela executa algumas funções da classe Session, como read() e write(),
      // registradas pelo session_set_save_handler. dentro dessas funções, algumas instruções de banco
      // são realizadas. quando um erro de banco acontecia e um echo() ou um Exception era jogado na tela, 
      // o código simplesmente parava e caracteres estranhos eram mostrados na tela.
      // FIX: capturar o Exception das classes de banco e dar um session_destroy() antes de mostrar (já feito abaixo)
    } 
    catch (Exception $e) {
      session_destroy(); // arruma o bug do Exception aparecer com caracteres estranhos (9/4/13)
      throw $e;
    }
    
  }
  
  static public function open($savePath, $session_name) {
    
    self::$savePath = $savePath.DS;
    if (!is_dir(self::$savePath)) {
        mkdir(self::$savePath, 0777);
    }
    return true;
  }

  static public function close() {
    return true;
  }

  static public function read($sid) {
    return self::_decryptData((string)@file_get_contents(self::$savePath."sess_$sid"), SESSION_NAME);
  }

  static public function write($sid, $data) {
    return file_put_contents(self::$savePath."sess_$sid", self::_encryptData($data)) === false ? false : true;
  }

  static public function destroy($sid) {
    
    $file = self::$savePath."sess_$sid";
    if (file_exists($file)) {
      unlink($file);
    }
    return true;
    
  }

  static public function gc($lifetime) {
    
    foreach (glob(self::$savePath ."sess_*") as $file) {
      if (filemtime($file) + $lifetime < time() && file_exists($file)) {
        unlink($file);
      }
    }

    return true;
    
  }

  /**
   * Re-gera o id da sessao. Deve ser chamado apos o login por uma questao
   * de seguranca.
   */
  static public function regenerate($old_sid) {
    
    $new_sid = self::generateId();
    
    return $success ? $new_sid : $old_sid;
  }

  static public function getId() {
    if (!self::isStarted()) {
      Session::start();
    }
    return session_id();
  }

  static public function setId($sid) {
    if (!self::isStarted()) {
      session_id($sid);
    }
  }

  static private function generateId() {
    $sid = g_sessionid();

    session_id($sid); //gera uma chave unica para cada visitante
    return $sid;
  }
  
  static private function _encryptData($str, $key) {
    return $str;
    
//    $block = mcrypt_get_block_size('des', 'ecb');
//    $pad = $block - (strlen($str) % $block);
//    $str .= str_repeat(chr($pad), $pad);
//
//    $iv = substr(md5(mt_rand(), true), 0, 8);
//
//    return base64_encode(mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB, $iv));
  }

  static private function _decryptData($str, $key) {
    return $str;
//    
//    $iv = substr(md5(mt_rand(), true), 0, 8);
//
//    $str = base64_decode($str);
//    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB, $iv);
//
//    $block = mcrypt_get_block_size('des', 'ecb');
//    $pad = ord($str[($len = strlen($str)) - 1]);
//    return substr($str, 0, strlen($str) - $pad);
  }

  public function count() {
    return count($_SESSION);
  }

  public function offsetExists($offset) {
    return isset($_SESSION[$offset]);
  }

  public function offsetGet($offset) {
    if (isset($this->reserved_names[$offset])) {
      throw new SessionException('Nome de session reservada do sistema, não é possível buscar '.
              'seu valor diretamente.');
    }
    return $_SESSION[$offset];
  }

  public function offsetSet($offset, $value) {
    if (isset($this->reserved_names[$offset])) {
      throw new SessionException('Nome de session reservada do sistema, não sobrescrever');
    }
    $_SESSION[$offset] = $value;
  }

  public function offsetUnset($offset) {
    if (isset($this->reserved_names[$offset])) {
      throw new SessionException('Nome de session reservada do sistema, não destruir');
    }
    unset($_SESSION[$offset]);
  }
  
}
