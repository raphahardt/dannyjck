<?php

Core::depends('Cookie');
Core::depends('Dbc');
Core::uses('Request', 'core/network');

class SessionException extends Exception {};

class Session implements ArrayAccess, Countable {
  
  private $reserved_names = array(
      SESSION_USER_NAME => 1, SESSION_TOKEN_NAME => 1, 'id' => 1, 'logged' => 1
  );
  
  static private $started = false;

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

  /**
   * Re-gera o id da sessao. Deve ser chamado apos o login por uma questao
   * de seguranca.
   */
  static public function regenerate($old_sid) {
    
    $new_sid = self::generateId();
    
    $dbc = Dbc::getInstance();
    $dbc->prepare('update fm_session set sid=? where sid = ?');
    $dbc->bind_param($new_sid);
    $dbc->bind_param($old_sid);
    $success = $dbc->execute();

    $dbc->free();
    
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
