<?php

Core::depends('Cookie');
Core::uses('SessionModel', 'model/session');
Core::uses('SessionModelCollection', 'model/session');

/*fwimport('session.SessionModel', 'model');
fwimport('session.SessionModelCollection', 'model');
fwimport('session.Cookie', 'core');*/

class Session implements ArrayAccess, Countable {
  
  private $reserved_names = array(
      SESSION_USER_NAME => 1, SESSION_TOKEN_NAME => 1, 'id' => 1, 'logged' => 1
  );
  
  static private $model;
  static private $started = false;
  
  static private $savePath;
  
  static public function isStarted() {
    return self::$started;
  }
  
  static public function start() {
    
    //self::$model = new SessionModelCollection();
    
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
    
    self::$savePath = $savePath;
    if (!is_dir(self::$savePath)) {
        mkdir(self::$savePath, 0777);
    }
    
    return true;
  }

  static public function close() {
    return true;
  }

  static public function read($sid) {
    
    return (string)@file_get_contents(self::$savePath."\\sess_$sid");
    
    $model =& self::$model;

    $model->setFilter(_c($model['sid'], '=', $sid));

    if ($model->select()) {
      return self::_decryptData($model[0]['sessao'], SESSION_NAME);
    }
    return '';
  }

  static public function write($sid, $data) {
    
    return file_put_contents(self::$savePath."\\sess_$sid", $data) === false ? false : true;
    
    $model =& self::$model;
    $ip = GlobalFunc::getIp();
    $timestamp = time();
    $data = self::_encryptData($data, SESSION_NAME);

    // pega o id do usuario logado
    $user = $_SESSION[ SESSION_USER_NAME ];
    $uid = (is_object($user) && $user->id) ? $user->id : 0;
    //$uid = $user->id;
    
    try {

      // procura se o usuario está gravado nas sessions
      // where (id=$uid and id>0) or sid=$sid
      $model->setFilter( new SQLExpression('OR', 
              new SQLExpression('AND', 
                      _c($model['id'], '=', $uid), 
                      _c($model['id'], '>', 0)), 
              _c($model['sid'], '=', $sid)) );

      $numrows = 0;
      if ($model->select()) {
        $numrows = $model->getTotal();
      }
      if ($numrows > 0) {
        // pega apenas o primeiro registro
        $session = $model[0];
      }

      // verifica quantos registros foram encontrados
      if ($numrows > 1) {

        // se tiver mais de um usuario logado ao mesmo tempo
        // where id=$result.id or sid=$sid
        $model->setFilter( new SQLExpression('OR', 
                _c($model['id'], '=', $session['id']), 
                _c($model['sid'],'=', $sid)) );
        // ...derruba sem dó
        $model->delete();

        // depois insere novamente com o mesmo sid e uid
        $session['id'] = $uid;
        $session['sid'] = $sid;
        $session['hostname'] = $ip;
        $session['timestamp'] = $timestamp;
        $session['sessao'] = $data;

        if ($session->insert()) {
          return true;
        }
      } elseif ($numrows == 1) {

        // se só tiver ele logado
        $session->setFilter('sid');
        $session->setFilterValues($session['sid']);
        $session['id'] = $uid;
        $session['sid'] = $sid;
        $session['hostname'] = $ip;
        $session['timestamp'] = $timestamp;
        $session['sessao'] = $data;

        if ($session->update()) {
          return true;
        }
      } else {

        // se ele não existir nas sessions
        $session = new SessionModel();
        $session['id'] = $uid;
        $session['sid'] = $sid;
        $session['hostname'] = $ip;
        $session['timestamp'] = $timestamp;
        $session['sessao'] = $data;

        if ($session->insert()) {
          return true;
        }
      }
    } catch (Exception $e){
      throw $e;
    }

    return false;
  }

  static public function destroy($sid) {
    
    $file = self::$savePath."\\sess_$sid";
    if (file_exists($file)) {
        unlink($file);
    }
    return true;
    
    $model =& self::$model;
    
    $model->setFilter(_c($model['sid'], '=', $sid));
    if ($model->delete()) {
      return true;
    }
    return false;
    
  }

  static public function gc($lifetime) {
    
    foreach (glob(self::$savePath."\\sess_*") as $file) {
        if (filemtime($file) + $lifetime < time() && file_exists($file)) {
            unlink($file);
        }
    }
    
    return true;
    
    $model =& self::$model;
    
    $time = time() - $lifetime;
    
    $model->setFilter(_c($model['timestamp'], '<', $time));
    if ($model->delete()) {
      return true;
    }
    return false;
    
  }

  /**
   * Re-gera o id da sessao. Deve ser chamado apos o login por uma questao
   * de seguranca.
   */
  static public function regenerate($old_sid) {
    
    $model = new SessionModel();

    $new_sid = self::generateId();
    
    $model['sid'] = $new_sid;
    $model->setFilter($model->Fields['sid']);
    $model->setFilterValues($old_sid);
    if ($model->update()) {
      return $new_sid;
    }
    return $old_sid;
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
    //$ip = GlobalFunc::getIp();
    $ip = '127.0.0.1';
    $ip = str_replace('/', '-', $ip);
    $ip = str_replace(array('.', ':'), '', $ip);
    $sid = '1-' . uniqid() . '-' . $ip . '-';
    $rand = rand(3, 6);

    for ($i = 0; $i < $rand; $i++) {
      $sid .= rand(5, 17);
    }

    session_id($sid); //gera uma chave unica para cada visitante
    return $sid;
  }
  
  static public function get($key) {
    return $_SESSION[$key];
  }
  
  static public function set($key, $value) {
    $_SESSION[$key] = $value;
  }
  
  static public function getToken() {
    return $_SESSION['token'];
  }
  
  static public function setToken($value) {
    $_SESSION['token'] = $value;
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

  static public function dump() {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
  }

  public function count() {
    return count($_SESSION);
  }

  public function offsetExists($offset) {
    return isset($_SESSION[$offset]);
  }

  public function offsetGet($offset) {
    if (isset($this->reserved_names[$offset])) {
      throw new AppException('Nome de session reservada do sistema, não é possível buscar '.
              'seu valor diretamente.');
    }
    return $_SESSION[$offset];
  }

  public function offsetSet($offset, $value) {
    if (isset($this->reserved_names[$offset])) {
      throw new AppException('Nome de session reservada do sistema, não sobrescrever');
    }
    $_SESSION[$offset] = $value;
  }

  public function offsetUnset($offset) {
    if (isset($this->reserved_names[$offset])) {
      throw new AppException('Nome de session reservada do sistema, não destruir');
    }
    unset($_SESSION[$offset]);
  }
  
}
