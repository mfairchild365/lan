<?php
namespace LAN;

interface Renderable
{
    /*
     * Should render an object as a HTML associative array view and return it.
     */
     function render();
}