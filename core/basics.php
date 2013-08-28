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

if (!function_exists('g_sessionid')) {
  
  /**
   * Gera uma string token unica
   * @return string
   */
  function g_sessionid() {
    return sprintf('%d-%s%s%u-%d', mt_rand(0,9), md5(env('HTTP_USER_AGENT')), md5(uniqid()), ip2long(env('REMOTE_ADDR')), mt_rand(0, 5));
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
  
  function dump() {
    echo '<pre>';
    print_r(array(
        DJCK,
        CORE_PATH,
        APP_PATH,
        SITE_FULL_URL,
        SITE_URL,
        STATIC_URL,
    ));
    echo '</pre>';
    exit;
  }
  
}