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
  
  function __construct($options = null, $initialize = true, $error_messages = null) {
    $options = array(
        'image_versions' => array(
            // Uncomment the following version to restrict the size of
            // uploaded images:
            /*
            '' => array(
                'max_width' => 1920,
                'max_height' => 1200,
                'jpeg_quality' => 95
            ),
            */
            // Uncomment the following to create medium sized images:
            /*
            'medium' => array(
                'max_width' => 800,
                'max_height' => 600,
                'jpeg_quality' => 80
            ),
            */
            'thumbnail' => array(
                // Uncomment the following to use a defined directory for the thumbnails
                // instead of a subdirectory based on the version identifier.
                // Make sure that this directory doesn't allow execution of files if you
                // don't pose any restrictions on the type of uploaded files, e.g. by
                // copying the .htaccess file from the files directory for Apache:
                //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
                //'upload_url' => $this->get_full_url().'/thumb/',
                // Uncomment the following to force the max
                // dimensions and e.g. create square thumbnails:
                //'crop' => true,
                'max_width' => 250,
                'max_height' => 350
            )
        )
    );
    parent::__construct($options, $initialize, $error_messages);
  }
  
  protected function handle_form_data($file, $index) {
    parent::handle_form_data($file, $index);
  }
  
}

$upload_handler = new DJCKUploadHandler();
