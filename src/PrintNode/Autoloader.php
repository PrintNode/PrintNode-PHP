<?php

namespace PrintNode;

/**
 * Autoloader
 *
 * The printnode autoloader.
 */
abstract class Autoloader
{
    
    /**
     * The PrintNode autoloader
     * 
     * PSR-4 Compliant: http://www.php-fig.org/psr/psr-4/
     * 
     * @param string $class
     * @return mixed
     */
    public static function autoload($class)
    {
        
        $nsLen = \mb_strlen(__NAMESPACE__);
        
        if (strncmp(__NAMESPACE__, $class, $nsLen) !== 0) {
            return;
        }
        
        $file = __DIR__ . DIRECTORY_SEPARATOR;
        $file.= str_replace('\\', DIRECTORY_SEPARATOR, mb_substr($class, ($nsLen+1))) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
        
    }
    
}