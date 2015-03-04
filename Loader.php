<?php

namespace PrintNode;

/**
 * PrintNode_Loader
 *
 * Zend / SPL autoloader for PHP5.
 */
abstract class PrintNode_Loader
{
    /**
     * Register autoLoad method with the SPL autoloader
     * @param void
     * @return void
     */
    public static function init()
    {
        spl_autoload_register(array(__CLASS__, 'autoLoad'));
    }

    /**
     * Class autoloaders
     * @param mixed $class
     * @return object
     */
    public static function autoLoad($class)
    {
        if (substr($class, 0, 9) == 'PrintNode') {

            include_once dirname(__FILE__). '/'. str_replace('PrintNode_', '', str_replace('\\', '/', substr($class, 10))). '.php';
            return $class;
        }

        return false;
    }
}