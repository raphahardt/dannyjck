<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {counter} function plugin
 *
 * Type:     function<br>
 * Name:     counter<br>
 * Purpose:  print out a counter value
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://www.smarty.net/manual/en/language.function.counter.php {counter}
 *       (Smarty online manual)
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string|null
 */
function smarty_function_component($params, $template)
{
    $file = $params['name'];
    if (!$file)
      return null;
    unset($params['name']);
    
    $tpl = new Smarty_Internal_Template(md5(mt_rand(0,200)), $template->smarty, $template, null, null, false, 0);
    
    $tpl->assign($params);
    $output = $tpl->fetch($file);
    
    return $output;
    
}

?>