<?php

namespace Basil\Mpesa\Contracts;

/**
 * Interface HttpRequest
 *
 * @category PHP
 *
 * @author   Kabangi Julius <kabangilive@gmail.com>
 *           Basil Ndonga <basilndonga@gmail.com>
 */


interface HttpRequest
{
    public function setOption($name, $value);
    public function execute();
    public function error();
    public function getInfo($name);
    public function close();
    public function reset();
}
