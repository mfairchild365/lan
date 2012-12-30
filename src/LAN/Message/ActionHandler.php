<?php
namespace LAN\Message;

class ActionHandler implements \LAN\ActionHandlerInterface
{
    public function handle($action, $data, \LAN\ConnectionContainer $editor)
    {

        $object = Record::createNewMessage($editor->getUser()->getID(), $data);

        $returnData           = array();
        $returnData['action'] = 'MESSAGE_NEW';
        $returnData['data']   = $object;

        return $returnData;
    }
}