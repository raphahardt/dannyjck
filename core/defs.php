<?php

date_default_timezone_set('America/Sao_Paulo');

// SITE --------------------------------------------------------------

/**
 * Titulo do site
 */
if (!defined('SITE_TITLE'))
  define('SITE_TITLE', 'CMS');

/**
 * Subtitulo do site
 */
if (!defined('SITE_SUBTITLE'))
  define('SITE_SUBTITLE', 'Content Management System');

/**
 * Autor do site
 */
if (!defined('SITE_OWNER'))
  define('SITE_OWNER', 'Raphael Hardt');

/**
 * Copyright relacionado ao site
 */
if (!defined('SITE_COPYRIGHT'))
  define('SITE_COPYRIGHT', 'Raphael Hardt - Creative Commons (BY-NC-ND) 3.0');

/**
 * Keywords
 */
if (!defined('SITE_KEYWORDS'))
  define('SITE_KEYWORDS', '');

/**
 * Description
 */
if (!defined('SITE_DESCRIPTION'))
  define('SITE_DESCRIPTION', '');

/**
 * Raiz de onde o site roda (path relativo ao DOC_ROOT configurado pelo server)
 */
if (!defined('SITE_URL')) {
  $root = str_replace('/', DS, env('DOCUMENT_ROOT'));
  $baseurl = str_replace(DS, '/', str_ireplace($root, '', ROOT));
  if (strpos($baseurl, '/') !== 0)
    $baseurl = '/'.$baseurl;
  
  if ($baseurl === '/') $baseurl = '';
  
  define('SITE_URL', $baseurl);
  
  unset($root, $baseurl);
}

/**
 * Host completo do site
 */
if (!defined('SITE_FULL_URL')) {
  $s = null;
  if (env('HTTPS')) {
    $s = 's';
  }

  $http_host = env('HTTP_HOST');
  if (!isset($http_host)) {
    $http_host = env('SERVER_NAME');
  }
  define('SITE_FULL_URL', 'http'.$s.'://'.$http_host . SITE_URL);
  
  unset($http_host, $s);
}

if (!defined('SITE_DOMAIN')) {
  define('SITE_DOMAIN', env('HTTP_BASE'));
}

/**
 * Host de onde estão os resources (imagens, js, css) estáticos
 */
if (!defined('STATIC_URL')) {
  define('STATIC_URL', SITE_FULL_URL.'/public');
}

/**
 * Charset do site
 */
if (!defined('SITE_CHARSET')) {
  define('SITE_CHARSET', 'utf-8');
}

/**
 * Se o site está offline ou não
 */
if (!defined('SITE_OFFLINE')) {
  define('SITE_OFFLINE', false);
}

// SESSION --------------------------------------------------------------------------

/**
 * Nome do cookie que vai gravar o ID da session atual. Deve ser um nome curto,
 * identificável e conter apenas letras, de preferencia maiusculas.
 */
if (!defined('SESSION_NAME'))
  define('SESSION_NAME', 'DJCKID');

/**
 * Numero em segundos do tempo que o cookie da session irá ficar disponível para
 * o usuário enquanto ele estiver em "idle". 0 (zero) significa que o cookie só
 * é apagado quando o browser é fechado.
 */
if (!defined('SESSION_TIMEOUT'))
  define('SESSION_TIMEOUT', 0); 

/**
 * Nome da session que irá guardar o token de acesso do usuário
 */
if (!defined('SESSION_TOKEN_NAME'))
  define('SESSION_TOKEN_NAME', 'token');

/**
 * Nome da session que irá guardar o usuário logado no site
 */
if (!defined('SESSION_USER_NAME'))
  define('SESSION_USER_NAME', 'user');


// COOKIES --------------------------------------------------------------------------

/**
 * Dominio onde os cookies serão gravados
 */
if (!defined('COOKIE_DOMAIN'))
  define('COOKIE_DOMAIN', SITE_DOMAIN);

/**
 * Pasta onde os cookies serão gravados
 */
if (!defined('COOKIE_PATH'))
  define('COOKIE_PATH', '/');


// CACHE ---------------------------------------------------------------------------

/**
 * Tempo máximo de cache para resources estáticos
 */
if (!defined('CACHE_STATIC_SIZE'))
  define('CACHE_STATIC_SIZE', 1 * 30 * 24 * 60 * 60); // 1 mes

/**
 * O mesmo que CACHE_STATIC_SIZE, mas em formato timestamp
 */
if (!defined('CACHE_STATIC_TIMESTAMP'))
  define('CACHE_STATIC_TIMESTAMP', time() + CACHE_STATIC_SIZE);