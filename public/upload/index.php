<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);

define('_DEFS_ONLY', true);
require_once '../../index.php';

require('UploadHandler.php');

class DJCKUploadHandler extends UploadHandler {
  
  protected function handle_form_data($file, $index) {
    parent::handle_form_data($file, $index);
  }
  
}

$upload_handler = new DJCKUploadHandler();
