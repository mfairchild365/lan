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

        if (!\LAN\Config::get('STEAM_API_KEY')) {
            return '[]';
        }

        $ids = array();

        foreach (\LAN\User\RecordList::getAllOnline() as $user) {
            $id = $user->getSteamID64();
            if (empty($id)) {
                continue;
            }

            $ids[] = $id;
        }

        if (empty($ids)) {
            return '[]';
        }

        $file = \LAN\Config::get('CACHE_DIR') . self::FILE_NAME;

        if (file_exists($file) && (filemtime($file) + \LAN\Config::get('STEAM_CACHE_TIMEOUT_PROFILES') > time())) {
            return file_get_contents($file);
        }

        $requestUrl = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?steamids='.implode(',', $ids).'&format=json&key='.\LAN\Config::get('STEAM_API_KEY');

        if (!$json = @file_get_contents($requestUrl)) {
            throw new \LAN\Exception('Could not retrieve steam profiles json');
        }

        file_put_contents($file, $json);

        return $json;
    }

}