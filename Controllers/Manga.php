<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/28
 * Time: 13:26
 */

namespace Controllers;

use Models\CEntertainment;
use Models\ImageCloud;
use Models\Scrapy;
use MongoDB\BSON\ObjectId;
use SimplePhp\Exception;

/**
 * @property  \Models\CEntertainment ce
 * @property \Models\ImageCloud ic
 * @property \Models\Scrapy scrapy
 */
class Manga extends ControllerBase
{
    /**
     * @throws \ReflectionException
     * @throws \SimplePhp\Exception
     */
    protected function onCreate()
    {
        $this->ce = new CEntertainment();
        $this->ic = new ImageCloud();
        $this->scrapy = new Scrapy();
        // TODO: Implement onCreate() method.
    }

    /**
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws \SimplePhp\Exception
     */
    public function Latest()
    {
        $latest = $this->ce->setResource("manga")->getLatest($_GET["limit"] ?? 10, $_GET["skip"] ?? 0);
        foreach ($latest as &$item) {
            $item->thumb = $this->ic->getThumbInfo($item->thumb_id);
            $item->info = $this->scrapy->getElementById($item->source, $item->source_id);
            unset($item->info->thumb_urls);
        }
        return $latest;
    }

    /**
     * @return array|bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getResourcesByIds()
    {
        if (isset($_GET["resource_ids"])) {
            $resource_ids = array();
            foreach (json_decode($_GET["resource_ids"]) as $resource_id) {
                $resource_ids[] = new ObjectId($resource_id);
            }
            $resources = $this->ce->setResource("manga")->getResourceByIds($resource_ids);
            foreach ($resources as &$item) {
                $item->thumb = $this->ic->getThumbInfo($item->thumb_id);
                $item->info = $this->scrapy->getElementById($item->source, $item->source_id);
                unset($item->info->thumb_urls);
            }
            return $resources;
        } else {
            throw new Exception("Less important param resource_ids!");
        }
    }

    /**
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws \SimplePhp\Exception
     */
    public function upClickedCount()
    {
        if (isset($_GET["resource_id"])) {
            return $this->ce->setResource("manga")->upClickedCount(new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("Less important param resource id!");
        }
    }

    /**
     * @throws \SimplePhp\Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getArtistOpus()
    {
        if (isset($_GET["artist"])) {
            $result = array();
            $opus = $this->scrapy->getArtistOpus("nhentai", $_GET["artist"], isset($_GET["limit"]) ? $_GET["limit"] : 10, isset($_GET["skip"]) ? $_GET["skip"] : 0);
            foreach ($opus as $opu) {
                $resource = $this->ce->setResource("manga")->getResourceBySourceId($opu->_id);
                $resource->info = $opu;
                $resource->thumb = $this->ic->getThumbInfo($resource->thumb_id);
                $result[] = $resource;
            }
            return $result;
        } else {
            throw new Exception("Less important param artist!");
        }
    }

    /**
     * @return array
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function search()
    {
        if (isset($_GET["search_content"])) {
            $search_content = json_decode($_GET["search_content"]);
            $result = array();
            $opus = $this->scrapy->getOpus("nhentai", $search_content, isset($_GET["limit"]) ? $_GET["limit"] : 10, isset($_GET["skip"]) ? $_GET["skip"] : 0);
            foreach ($opus as $opu) {
                $resource = $this->ce->setResource("manga")->getResourceBySourceId($opu->_id);
                $resource->info = $opu;
                $resource->thumb = $this->ic->getThumbInfo($resource->thumb_id);
                $result[] = $resource;
            }
            return $result;
        } else {
            throw new Exception("Less important param search_content!");
        }
    }
}