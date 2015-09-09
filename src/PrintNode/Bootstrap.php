<?php

/**
 * Autoloader Bootstrap File
 * 
 * Include this file in your script to add the PrintNode Autoloader.
 * 
 */

require_once('Autoloader.php');

\spl_autoload_register('\PrintNode\Autoloader::autoload');