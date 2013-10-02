<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('env')) {

  /**
   * Gets an environment variable from available sources, and provides emulation
   * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
   * IIS, or SCRIPT_NAME in CGI mode). Also exposes some additional custom
   * environment information.
   *
   * @param string $key Environment variable name.
   * @return string Environment variable setting.
   * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#env
   */
  function env($key) {
    if ($key === 'HTTPS') {
      if (isset($_SERVER['HTTPS'])) {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
      }
      return (strpos(env('SCRIPT_URI'), 'https://') === 0);
    }

    if ($key === 'SCRIPT_NAME') {
      if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
        $key = 'SCRIPT_URL';
      }
    }

    $val = null;
    if (isset($_SERVER[$key])) {
      $val = $_SERVER[$key];
    } elseif (isset($_ENV[$key])) {
      $val = $_ENV[$key];
    } elseif (getenv($key) !== false) {
      $val = getenv($key);
    }

    if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
      $addr = env('HTTP_PC_REMOTE_ADDR');
      if ($addr !== null) {
        $val = $addr;
      }
    }

    if ($val !== null) {
      return $val;
    }

    switch ($key) {
      case 'DOCUMENT_ROOT':
        $name = env('SCRIPT_NAME');
        $filename = env('SCRIPT_FILENAME');
        $offset = 0;
        if (!strpos($name, '.php')) {
          $offset = 4;
        }
        return substr($filename, 0, -(strlen($name) + $offset));
      case 'PHP_SELF':
        return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
      case 'CGI_MODE':
        return (PHP_SAPI === 'cgi');
      case 'HTTP_BASE':
        $host = env('HTTP_HOST');
        $parts = explode('.', $host);
        $count = count($parts);

        if ($count === 1) {
          return '.' . $host;
        } elseif ($count === 2) {
          return '.' . $host;
        } elseif ($count === 3) {
          $gTLD = array(
              'aero',
              'asia',
              'biz',
              'cat',
              'com',
              'coop',
              'edu',
              'gov',
              'info',
              'int',
              'jobs',
              'mil',
              'mobi',
              'museum',
              'name',
              'net',
              'org',
              'pro',
              'tel',
              'travel',
              'xxx'
          );
          if (in_array($parts[1], $gTLD)) {
            return '.' . $host;
          }
        }
        array_shift($parts);
        return '.' . implode('.', $parts);
    }
    return null;
  }

}

if (!function_exists('g_token')) {
  
  /**
   * Gera uma string token unica
   * @return string
   */
  function g_token() {
    return md5(uniqid()).mt_rand(5, 15).mt_rand(0, 5);
  }
  
}

if (!function_exists('fmt_value')) {
  
  /**
   * Formata um valor com um sufixo quando o número encontra o numero de casas decimais
   * definidas em $suffix
   * Exemplos: 1 200 = 1,2 mil
   *           20 413 000 = 20,4 milhões
   * @param type $value Valor a ser formatado
   * @param type $base Base númerica do valor a ser formatado. Padrão é 1000
   * @param type $decimals Quantas casas decimais mostrar após a virgula. Padrão é 1
   * @param type $suffix O que mostrar após o número quando encontrar o log() do valor na
   *                     $base definida. Deve ser um array onde o key é o log() desejado
   *                     e o valor pode ser uma string ou um array com dois valores (um
   *                     para a escrita no singular e outro para escrita no plural)
   */
  function fmt_value($value, $base=1000, $decimals=1, $suffix=array(
      1=>'mil',
      2=>array('milhão','milhões'),
      3=>array('bilhão','bilhões')) ) {
    $key = floor(log($value, $base)); // pega o log que vai ser o key dos sufixos
    $num = $value / (pow($base, $key)); // cria o numero que vai ser o valor a ser mostrado
    $suf = (is_array($suffix[$key])) ? 
             ((int)$num != 1 ? $suffix[$key][1] : $suffix[$key][0]) :
             $suffix[$key];
    
    $num_fmted = round($num, $decimals);
    
    return "$num_fmted $suf";
  }
  
  /**
   * Formata um valor em sufixo em bytes
   * Exemplo: 1024 = 1 KB
   * @param type $value Valor a ser formatado
   * @return string String com valor formatado, com sufixo (B,KB,MB,GB,TB)
   */
  function fmt_bytes($value) {
    return fmt_value($value, 1000, 3, array(
       0=>'B',
       1=>'KB',
       2=>'MB',
       3=>'GB',
       4=>'TB',
    ));
  }
  
  /**
   * Formata um valor em sufixo em bibytes
   * Exemplo: 1024 = 1 KiB
   * @param type $value Valor a ser formatado
   * @return string String com valor formatado, com sufixo (B,KiB,MiB,GiB,TiB)
   */
  function fmt_bibytes($value) {
    return fmt_value($value, 1024, 3, array(
       0=>'B',
       1=>'KiB',
       2=>'MiB',
       3=>'GiB',
       4=>'TiB',
    ));
  }
  
}

if (!function_exists('json')) {
  
  if (!defined('JSON_FORCE_OBJECT')) {
    define('JSON_FORCE_OBJECT', 16);
  }
  
  /**
   * Codifica um array para uma string json
   * Normaliza as funções das versões 5 até 5.4
   * @return string
   */
  function json($string, $options = 0) {
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
      return json_encode($string, $options);
    } else {
      if (($options & JSON_FORCE_OBJECT) == JSON_FORCE_OBJECT) {
        $new_string = array();
        foreach ($string as $key => $v) {
          $new_string[(string)$key] = $v;
        }
        $string = $new_string;
        unset($new_string);
      }
      
      return json_encode($string);
    }
  }
  
}

if (!function_exists('array_column')) {

  /**
   * Returns the values from a single column of the input array, identified by
   * the $columnKey.
   *
   * Optionally, you may provide an $indexKey to index the values in the returned
   * array by the values from the $indexKey column in the input array.
   *
   * @param array $input A multi-dimensional array (record set) from which to pull
   *                     a column of values.
   * @param mixed $columnKey The column of values to return. This value may be the
   *                         integer key of the column you wish to retrieve, or it
   *                         may be the string key name for an associative array.
   * @param mixed $indexKey (Optional.) The column to use as the index/keys for
   *                        the returned array. This value may be the integer key
   *                        of the column, or it may be the string key name.
   * @return array
   */
  function array_column($input = null, $columnKey = null, $indexKey = null) {
    // Using func_get_args() in order to check for proper number of
    // parameters and trigger errors exactly as the built-in array_column()
    // does in PHP 5.5.
    $argc = func_num_args();
    $params = func_get_args();

    if ($argc < 2) {
      trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
      return null;
    }

    if (!is_array($params[0])) {
      trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
      return null;
    }

    if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
    ) {
      trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
      return false;
    }

    if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
    ) {
      trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
      return false;
    }

    $paramsInput = $params[0];
    $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

    $paramsIndexKey = null;
    if (isset($params[2])) {
      if (is_float($params[2]) || is_int($params[2])) {
        $paramsIndexKey = (int) $params[2];
      } else {
        $paramsIndexKey = (string) $params[2];
      }
    }

    $resultArray = array();

    foreach ($paramsInput as $row) {

      $key = $value = null;
      $keySet = $valueSet = false;

      if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
        $keySet = true;
        $key = (string) $row[$paramsIndexKey];
      }

      if ($paramsColumnKey === null) {
        $valueSet = true;
        $value = $row;
      } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
        $valueSet = true;
        $value = $row[$paramsColumnKey];
      }

      if ($valueSet) {
        if ($keySet) {
          $resultArray[$key] = $value;
        } else {
          $resultArray[] = $value;
        }
      }
    }

    return $resultArray;
  }

}

if (!function_exists('dump')) {
  
  function dump($var) {
    $props = _var_props($var);
    
    $colors = array(
        'Dbc.php' => array('salmon', 'white'),
        'FileMapper.php' => array('yellow', 'black'),
    );
    list($backc, $forec) = isset($colors[ $props['file'] ]) ? 
            $colors[ $props['file'] ] : 
            array('cyan', 'blue');
    echo '<pre style="border:1px solid '.$backc.';background:white;font-family:Consolas,monospaced;font-size:13px;">';
    echo '<div style="color:'.$forec.';background:'.$backc.';padding:10px;">';
    echo '<strong>Nome: '.$props['name'].'</strong><br>';
    echo '<strong>Arquivo: '.$props['file'].' linha '.$props['line'].'</strong><br>';
    echo '</div><div style="padding:10px">';
    if (is_array($var) || is_object($var))
      print_r($var);
    else
      var_dump($var);
    /*print_r(array(
        DJCK,
        CORE_PATH,
        APP_PATH,
        SITE_FULL_URL,
        SITE_URL,
        STATIC_URL,
    ));*/
    echo '</div>';
    echo '</pre>';
    //exit;
  }
  
  function _var_props( $v ) {
    $trace = debug_backtrace();
    $vLine = file( $trace[1]['file'] );
    $fLine = $vLine[ $trace[1]['line'] - 1 ];
    preg_match( "#dump\((\\$?(\w+)(\s*(::|\->)\w+\s*\(?[^\)]*\)?|\[[^\]]*\])*)#", $fLine, $match );
    return array(
        'name' => $match[1] ? $match[1] : 'unknown',
        'file' => basename($trace[1]['file']),
        'line' => $trace[1]['line'],
        'line_string' => $fLine,
    );
  }
  
  function finish() {
    $end = microtime(true) - START;
    echo '<pre style="border:1px solid purple;background:white;font-family:Consolas,monospaced;font-size:13px;">';
    echo '<div style="color:white;background:purple;padding:10px;">';
    echo '<strong>Tempo gasto: '.($end*1000).'ms ('.(round($end, 2)).' segundos)</strong><br>';
    echo '</div></pre>';
    exit;
  }
  
}

if (!function_exists('str_putcsv')) {

  /**
   * Transforma um array em uma string em CSV.
   * ex:
   * array('col1', 'col2', 'col3 aaa', '', 'col5') -> col1,col2,"col3 aaa",,col5
   * @param array $input Array que será convertido
   * @param string $delimiter Caractere que será o separador. Padrão: vírgula
   * @param string $enclosure Caractere que será o encapsulador. Padrão: aspas
   * @return string
   */
  function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
    // Open a memory "file" for read/write...
    $fp = fopen('php://temp', 'r+');
    // ... write the $input array to the "file" using fputcsv()...
    fputcsv($fp, $input, $delimiter, $enclosure);
    // ... rewind the "file" so we can read what we just wrote...
    rewind($fp);
    // ... read the entire line into a variable...
    $data = fgets($fp);
    // ... close the "file"...
    fclose($fp);
    // ... and return the $data to the caller, with the trailing newline from fgets() removed.
    return rtrim($data, "\n");
  }

}

if (!function_exists('str_getcsv')) {

  /**
   * Transforma uma string em CSV em um array com os valores.
   * ex:
   * col1,col2,"col3 aaa",,col5 -> array('col1', 'col2', 'col3 aaa', '', 'col5')
   * @param string $input Linha CSV a ser convertida
   * @param string $delimiter Caractere que será o separador. Padrão: vírgula
   * @param string $enclosure Caractere que será o encapsulador. Padrão: aspas
   * @param string $escape Caractere que será o escape do encapsulador e de caracteres especiais
   * @return array
   */
  function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\') {
    // Open a memory "file" for read/write...
    $fp = fopen('php://temp', 'r+');
    // ... write the $input array to the "file" using fputcsv()...
    fputs($fp, $input);
    // ... rewind the "file" so we can read what we just wrote...
    rewind($fp);
    // ... read the entire line into a variable...
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
      $data = fgetcsv($fp, 4196, $delimiter, $enclosure, $escape);
    } else {
      $data = fgetcsv($fp, 4196, $delimiter, $enclosure);
    }
    // ... close the "file"...
    fclose($fp);
    // ... and return the $data to the caller, with the trailing newline from fgets() removed.
    return $data;
  }

}