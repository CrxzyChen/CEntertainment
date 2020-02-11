<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 15:30
 */

namespace Models;

class ImageCloud extends DBModel
{
    protected function onInitial()
    {
        // TODO: Implement onInitial() method.
    }

    protected function onCreate()
    {
        $this->connect->Database("image_cloud");
        // TODO: Implement onCreate() method.
    }

    public function getThumb(int $thumb_id)
    {
        return $this->connect->Collection("image_pool")->findOne(array("thumb_id" => $thumb_id));
    }
}