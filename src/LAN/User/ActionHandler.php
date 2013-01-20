<?php
namespace LAN\User;

class ActionHandler implements \LAN\ActionHandlerInterface
{
    public function handle($action, $data,  \LAN\ConnectionContainer $editor)
    {
        if (!isset($data['id'])) {
            throw new \LAN\Exception("ID must be passed.");
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