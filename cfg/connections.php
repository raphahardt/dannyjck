<?php

Core::depends('DbcConfig');

DbcConfig::set('local', array(
  '#host' => 'localhost',
  '#user' => 'root',
  '#password' => 'lkglby90',
  '#schema' => 'reacaoed_main'
));

DbcConfig::set('fw', array(
  '#host' => 'localhost',
  '#user' => 'root',
  '#password' => '',
  '#schema' => base64_decode('ZmFzdG1vdG9ycw==')
));

DbcConfig::set('reacao', array(
  '#host' => 'localhost',
  '#user' => 'reacaoed_root',
  '#password' => 'hPuV4(,}#(7=',
  '#schema' => 'reacaoed_main'
));

DbcConfig::set('default', DbcConfig::get('reacao'));