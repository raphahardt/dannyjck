<?php

/**
 * Registra todos os controllers usados pelo core.
 * Geralmente, não possui nenhum
 * 
 * Para registrar controllers, utilize o APP/cfg/controllers.php
 */

Core::register('HomeController', 'controller/home');
Core::register('ErrorController', 'controller/home');
Core::register('StaticContentController', 'controller/home');