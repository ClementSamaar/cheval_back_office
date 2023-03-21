<?php

class Constants
{
    const CORE_PATH = '/Core/';
    const CONTROLLER_PATH = '/Controllers/';
    const MODELS_PATH = '/Models/';
    const VIEWS_PATH = '/Views/';

    public static function getRoot() : string
    {
        return realpath( __DIR__ . '/../');
    }

    public static function getPath(string $path) : string
    {
        switch ($path) {
            case 'core' :
                $required_path = self::CORE_PATH;
                break;
            case 'controllers' :
                $required_path = self::CONTROLLER_PATH;
                break;
            case 'models' :
                $required_path = self::MODELS_PATH;
                break;
            case 'views' :
                $required_path = self::VIEWS_PATH;
                break;
            default :
                $required_path = '';
        }

        return self::getRoot() . $required_path;
    }
}