<?php

namespace LAN\Steam;

class Profiles {

    const FILE_NAME = 'steam_profiles.json';

    private function __construct()
    {
        //singleton
    }

    public static function getJSON()
    {

        $ids = array('76561197990996324');

        $file = \LAN\Config::get('CACHE_DIR') . self::FILE_NAME;

        if (file_exists($file) && (filemtime($file) + \LAN\Config::get('STEAM_CACHE_TIMEOUT_PROFILES') > time())) {
            return file_get_contents($file);
        }

        $requestUrl = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?steamids='.implode(',', $ids).'&format=json&key='.\LAN\Config::get('STEAM_API_KEY');

        $data = @file_get_contents($requestUrl);

        file_put_contents($file, $data);

        return $data;
    }

}