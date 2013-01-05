<?php
namespace LAN\Steam;

class ActionHandler implements \LAN\ActionHandlerInterface
{
    public function handle($action, $data,  \LAN\ConnectionContainer $editor)
    {
        $json = \LAN\Steam\Profiles::getJSON();

        $returnData = array();
        $returnData['action'] = 'STEAM_PROFILES';
        $returnData['data']   = $json;

        return $returnData;
    }
}