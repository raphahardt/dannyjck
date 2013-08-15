<?php

require 'core/bootstrap.php';

$Router->dispatch($_GET['q']);

Core::dump();
//dump();