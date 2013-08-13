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

define('DEFS_ONLY', true);

define('DS', DIRECTORY_SEPARATOR);

define('DJCK', dirname(__FILE__));
define('CORE_PATH', dirname(DJCK).DS.'core');
define('PLUGIN_PATH', dirname(DJCK).DS.'plugins');

require CORE_PATH.DS.'bootstrap.php';

dump();