<?php
namespace LAN;

interface Renderable
{
    /*
     * Should render an object as a JSON view and return it.
     */
     function render();
}