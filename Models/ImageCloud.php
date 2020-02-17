<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 15:30
 */

namespace Models;

use SimplePhp\Network;

class ImageCloud
{
    public function getThumbInfo(int $thumb_id)
    {
        $url = "http://10.0.0.2:4396/?method=getThumbInfo&thumb_id=$thumb_id";
        return json_decode(Network::get($url));
    }
}