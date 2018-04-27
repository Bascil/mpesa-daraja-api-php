<?php

namespace Bascil\Mpesa;

use Bascil\Mpesa\Engine\Core;
use Bascil\Mpesa\Engine\Cache;
use Bascil\Mpesa\Engine\Config;
use Bascil\Mpesa\Engine\MpesaTrait;
use Bascil\Mpesa\Auth\Authenticator;
use Bascil\Mpesa\Engine\CurlRequest;
/**
 * Class Mpesa
 *
 * @category PHP
 *
 * @author   Julius Kabangi <kabangijulius@gmail.com>
             Basil Ndonga   <basilndonga@gmail.com>
 */
class Init
{
    use MpesaTrait;

    /**
     * @var Core
     */
    private $engine;

    /**
     * Mpesa constructor.
     *
     */
    public function __construct($myconfig = []){
        $config = new Config($myconfig);
        $cache = new Cache($config);
        $auth = new Authenticator();
        $httpClient = new CurlRequest();
        $this->engine = new Core($config, $cache,$httpClient,$auth);
    }
}
