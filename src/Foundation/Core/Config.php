<?php
namespace Zan\Framework\Foundation\Core;

use Zan\Framework\Foundation\Exception\System\InvalidArgument;
use Zan\Framework\Utilities\Types\Dir;

class Config
{
    private static $configPath = '';
    private static $configMap = [];
    private static $inited = false;

    public static function init()
    {
        if (self::$inited) return true;

        self::$inited = true;
        self::setRunMode();
        self::clear();
    }

    public static function isInited()
    {
        return self::$inited;
    }

    private static function setRunMode()
    {
        switch (self::env('RUN_MODE')) {
            case 'test':
                self::set('run_mode','test');
                break;
            case 'unittest':
                self::set('run_mode','unittest');
                break;
            case 'readonly':
                self::set('run_mode','readonly');
                break;
            default:
                self::set('run_mode','online');
                break;
        }
        self::set('debug', Config::env('DEBUG') ? true : false);
    }

    public static function setConfigPath($path)
    {
        if(!$path || !is_dir($path)) {
            throw new InvalidArgument('invalid path for Config ' . $path);
        }
        self::$configPath = Dir::formatPath($path);
    }

    public static function env($key)
    {
        return get_cfg_var('kdt.'.$key);
    }

    public static function get($key)
    {
        $keys = explode('.',$key);
        $config = [];
        do {
            $key = array_shift($keys);
            if (!isset(self::$configMap[$key])) {
                $config = self::getConfigFile($key);
            }
        } while (!empty($keys));

        return $config;
    }

    public static function set($key,$value)
    {
        self::$configMap[$key]   = $value;
    }

    public static function clear()
    {
        self::$configMap = [];
    }

    private static function getConfigFile($key)
    {
        $envRunMode = self::$configMap['run_mode'] == 'online' ? 'online' : 'test';
        $configFile = self::$configPath . '/' .$envRunMode.'/'. $key . '.php';

        if(!file_exists($configFile)) {
            throw new InvalidArgument('No such config file ' . $configFile);
        }
        return self::$configMap[$key] = require $configFile;
    }
}