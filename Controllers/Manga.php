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
            $item->thumb = $this->ic->getThumb($item->thumb_id);
            $item->info = $this->scrapy->getElementById($item->source, $item->source_id);
            unset($item->info->thumb_urls);
        }
        return $latest;
    }


    /**
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws \SimplePhp\Exception
     */
    public function upClickedCount()
    {
        return $this->ce->setResource("manga")->upClickedCount(isset($_GET["resource_id"]) ? $_GET["resource_id"] : null);
    }
}