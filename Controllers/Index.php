<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/11
 * Time: 20:46
 */

namespace Controllers;


use SimplePhp\Network;

class Index extends ControllerBase
{

    protected function onCreate()
    {
        // TODO: Implement onCreate() method.
    }

    public function test(){
        $response = Network::get("http://10.0.0.2");
        var_dump($response);
    }
}