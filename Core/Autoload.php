<?php

require 'Core/Constants.php';

class Autoload
{
    private static function load(string $file) : void {
        if (is_readable($file)) require $file;
    }

    public static function loadCoreClass(string $class) : void {
        self::load(Constants::getPath('core') . $class . '.php');
    }

    public static function loadControllersClass(string $class) : void {
        self::load(Constants::getPath('controllers') . $class . '.php');
    }

    public static function loadModelsClass(string $class) : void {
        self::load(Constants::getPath('models') . $class . '.php');
    }

    public static function loadViewsClass(string $class) : void {
        self::load(Constants::getPath('view') . $class . '.php');
    }
}

spl_autoload_register('\Autoload::loadCoreClass');
spl_autoload_register('Autoload::loadControllersClass');
spl_autoload_register('Autoload::loadModelsClass');
spl_autoload_register('Autoload::loadViewsClass');