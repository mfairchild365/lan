<?php
namespace LAN\User;

class ActionHandler implements \LAN\ActionHandlerInterface
{
    public function handle($action, $data,  \LAN\ConnectionContainer $editor)
    {
        if (!isset($data['id'])) {
            throw new \LAN\Exception("ID must be passed.");
        }

        if (isset($data['steam'])) {
            if (!filter_var($data['steam'], FILTER_VALIDATE_URL)) {
                throw new \LAN\Exception("Steam Profile must be a URL");
            }

            if (!$xml = @file_get_contents($data['steam'] . '?xml=1')) {
                throw new \LAN\Exception("Sorry, I am unable to access that steam profile URL...");
            }

            if (!$xml = simplexml_load_string($xml)) {
                throw new \LAN\Exception("Profile data must be in XML format.");
            }

            $data['steam_id_64'] = ((string)$xml->steamID64);
        }

        $object = record::getByID($data['id']);

        $object->synchronizeWithArray($data);

        $object->save();

        $returnData = array();
        $returnData['action'] = 'USER_UPDATED';
        $returnData['data']   = $object;

        return $returnData;
    }
}