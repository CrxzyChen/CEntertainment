<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 14:42
 */

namespace Models;

/**
 * @property string resource
 */
class CEntertainment extends DBModel
{
    protected function onInitial()
    {
        // TODO: Implement onInitial() method.
    }


    protected function onCreate()
    {
        $this->connect->Database("centertainment");
    }

    /**
     * @param string $resource
     * @return CEntertainment
     */
    public function setResource(string $resource): CEntertainment
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return mixed
     * @throws \SimplePhp\Exception
     */
    public function getLatest($limit = 10, $skip = 0)
    {
        $this->isSetResource();
        return $this->connect->Collection("{$this->resource}_resource")->find(array(), array("limit" => $limit, "skip" => $skip, "sort" => array("thumb_id" => -1)));
    }

    /**
     * @throws \SimplePhp\Exception
     */
    private function isSetResource()
    {
        if (!$this->resource) {
            throw new \SimplePhp\Exception("Resource is not appointed!");
        }
    }
}