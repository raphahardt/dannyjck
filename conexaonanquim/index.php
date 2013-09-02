<?php
/**
 * Conexão Nanquim (Reação Editora)
 * 
 * Powered by Dannyjck (http://github.com/raphahardt/dannyjck)
 * Desenvolvido por Raphael Hardt (raphael dot hardt at gmail dot com)
 * (Inspirado e baseado em CakePHP (MIT), Joomla (GPL) e outras ferramentas)
 *
 * @copyright     Raphael Hardt
 * @link          http://www.conexaonanquim.com.br Conexão Nanquim Revista Digital
 * @since         Dannyjck 0.1v
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

define('DS', DIRECTORY_SEPARATOR);

define('ROOT', dirname(__FILE__));
define('CORE_PATH', dirname(ROOT).DS.'core');

require CORE_PATH.DS.'bootstrap.php';

// inicia a rota
if (isset($Router))
  $Router->dispatch($_GET['q']);