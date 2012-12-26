<?php
namespace LAN;

interface ActionHandlerInterface
{
    /*
     * Should handle an action
     *
     * @return mixed, (array['action'] and array['data'] defined to send a message to everyone, otherwise null.
     */
    function handle($action, $data, \LAN\ConnectionContainer $editor);
}