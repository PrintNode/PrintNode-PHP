<?php

/**
 * Autoloader Bootstrap File
 * 
 * Include this file in your script to add the PrintNode Autoloader.
 * 
 * The bootstrap file assumes that all the PrintNode files are located in a 
 * subdirectory called 'PrintNode'.
 * 
 */

require_once('PrintNode/Autoloader.php');

\spl_autoload_register('\PrintNode\Autoloader::autoload');