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
     * @throws \SimplePhp\Exception
     */
    public function Latest()
    {
        $latest = $this->ce->setResource("manga")->getLatest($_GET["limit"] ?? 10, $_GET["skip"] ?? 0);
        foreach ($latest as &$item) {
            $item->thumb = $this->ic->getThumb($item->thumb_id);
            $item->info = $this->scrapy->{$item->source}->getElementById($item->source_id);
            unset($item->info->thumb_urls);
        }
        return $latest;
    }
}