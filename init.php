<?php

// protege o arquivo
(_DJCK === true) or exit;

// inicia todo o sistema
define('DS', DIRECTORY_SEPARATOR);

$basedir = dirname(__FILE__).DS;
$root = str_replace('/', DS, $_SERVER['DOCUMENT_ROOT']);
$baseurl = str_replace(DS, '/', str_ireplace($root, '', $basedir));

define('DJK_BASE', $basedir);
define('DJK_URL', $baseurl);

// carrega arquivos principais do sistema
include DJK_BASE . DS . 'core' . DS . 'DJKApp.class.php';
include DJK_BASE . DS . 'core' . DS . 'router' . DS . 'DJKRouter.class.php';
include DJK_BASE . DS . 'core' . DS . 'loader' . DS . 'DJKLoader.class.php';