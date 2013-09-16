<?php

require 'core/bootstrap.php';

if (isset($Router))
  $Router->dispatch();