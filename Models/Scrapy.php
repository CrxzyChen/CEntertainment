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

    public function getOpus(string $source, \stdClass $search_condition, int $limit = 10, int $skip = 0)
    {
        $query = array();
        if (!empty($search_condition->mark)) {
            $query = array_merge_recursive($query, array("tags" => array('$in' => $search_condition->mark)));
        }
        if (!empty($search_condition->filter)) {
            $query = array_merge_recursive($query, array("tags" => array('$nin' => $search_condition->filter)));
        }
        if (!empty($search_condition->language)) {
            $query = array_merge_recursive($query, array("languages" => array('$in' => $search_condition->language)));
        }
        return $this->{$source}->find($query, array("limit" => $limit, "skip" => $skip, "projection" => array("thumb_urls" => 0)));
    }
}