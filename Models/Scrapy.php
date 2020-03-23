<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 15:46
 */

namespace Models;

use MongoDB\BSON\ObjectId;

class Scrapy extends DBModel
{
    protected function onCreate()
    {
        // TODO: Implement onCreate() method.
    }

    /**
     * @param $source
     * @param $id
     * @return mixed
     */
    public function getElementById(string $source, ObjectId $id)
    {
        return $this->{$source}->findOne(array("_id" => $id), array("projection" => array("thumb_urls" => 0)));
    }

    public function getArtistOpus(string $source, string $artist, int $limit = 10, int $skip = 0)
    {
        return $this->{$source}->find(array("artists" => array('$in' => array($artist))), array("limit" => $limit, "skip" => $skip, "projection" => array("thumb_urls" => 0)));
    }

    protected function setDatabase(&$database)
    {
        $database = "scrapy";
    }

}