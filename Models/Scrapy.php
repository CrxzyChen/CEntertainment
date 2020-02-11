<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 15:46
 */

namespace Models;

class Scrapy extends DBModel
{

    protected function onInitial()
    {
        // TODO: Implement onInitial() method.
    }

    protected function onCreate()
    {
        $this->connect->Database("scrapy");
        // TODO: Implement onCreate() method.
    }

    public function getElementById($source, $id)
    {
        return $this->connect->Collection($source)->findOne(array("_id" => $id));
    }
}