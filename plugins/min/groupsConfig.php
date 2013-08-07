<?php

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 * */
//<script type="text/javascript" src="{$siteURL}/min?b=js&amp;f=,,global/allpages.js,global/admin.js,lib/jquery.cookie.js,lib/jquery.form-2.8.3.js,,lib/hoverIntent.js,global.js,,lib/numberformat.js,"></script>

$base = array();
if (defined('DJCK_APP')) {
  $app_groups = (include DJCK_APP . DS . 'cfg' . DS . 'min_groupscfg.php');
} 

if (defined('DJCK_CORE')) {
  $core_groups = (include DJCK_CORE . DS . 'cfg' . DS . 'min_groupscfg.php');
}

return array_merge($base, $core_groups, $app_groups);