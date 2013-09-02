<?php

require 'core/bootstrap.php';

if (isset($Router))
  $Router->dispatch($_GET['q']);