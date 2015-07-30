<?php

$apiKey = getenv('PRINTNODE_API_KEY');

if (!$apiKey) {
    $err = <<<TEXT
The unit tests require a PrintNode API key to be set to execute. Please set enviroment variable PRINTNODE_API_KEY before running tests.

#!/bin/bash
export PRINTNODE_API_KEY="<insert your api key here>"


TEXT;
    die($err);
} else {
    define("API_KEY", $apiKey);
}

# see, http://www.php-fig.org/psr/psr-4/examples/
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'PrintNode';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/PrintNode';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }

});