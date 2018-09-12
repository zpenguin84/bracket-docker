<?php

namespace Application;

use Symfony\Component\Yaml\Yaml;

class Config
{
    public static function getHost(): string
    {
//        return '127.0.0.1';
        return '0.0.0.0';
    }

    public static function getPort()
    {
        $config = Yaml::parse(file_get_contents('config.yml'));
        return $config['port'] ?? null;
    }
}