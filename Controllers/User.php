<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/11
 * Time: 20:49
 */

namespace Controllers;


use Models\CEntertainment;
use Models\ImageCloud;
use Models\Scrapy;
use MongoDB\BSON\ObjectId;
use SimplePhp\Exception;

/**
 * Class User
 * @package Controllers
 * @property CEntertainment $ce
 * @property ImageCloud ic
 * @property  Scrapy scrapy
 */
class User extends ControllerBase
{
    const IMAGE_NOT_CACHED = 0;
    const IMAGE_COVER_DOWNLOADING = 1;
    const IMAGE_COVER_DOWNLOADED = 2;
    const IMAGE_ALL_DOWNLOADING = 3;
    const IMAGE_ALL_DOWNLOADED = 4;

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
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws \SimplePhp\Exception
     */
    public function addUser()
    {
        $username = "admin";
        $password = "cxyangghgh123520";
        return $this->ce->addUser($username, $password);
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addHistory()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            return $this->ce->addHistory($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addFocusArtist()
    {
        if (isset($_GET["uid"]) && isset($_GET["artist_name"])) {
            return $this->ce->addFocusArtist($_GET["uid"], $_GET["artist_name"]);
        } else {
            throw new Exception("less necessary parameter!");
        }
    }


    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function removeFocusArtist()
    {
        if (isset($_GET["uid"]) && isset($_GET["artist_name"])) {
            return $this->ce->removeFocusArtist($_GET["uid"], $_GET["artist_name"]);
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function isFocusArtist()
    {
        if (isset($_GET["uid"]) && isset($_GET["artist_name"])) {
            return $this->ce->isFocusArtist($_GET["uid"], $_GET["artist_name"]);
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function removeLike()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            return $this->ce->removeLike($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function isLike()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            return $this->ce->isLike($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addSubscribe()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            $resource = $this->ce->setResource("manga")->getResourceById(new ObjectId($_GET["resource_id"]));
            if ($this->ic->getThumbInfo($resource->thumb_id)->status < self::IMAGE_ALL_DOWNLOADING) {
                $this->ic->download($resource->thumb_id);
            }
            return $this->ce->addSubscribe($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function removeSubscribe()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            return $this->ce->removeSubscribe($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function isSubscribe()
    {
        if (isset($_GET["uid"]) && isset($_GET["resource_id"])) {
            return $this->ce->isSubscribe($_GET["uid"], new ObjectId($_GET["resource_id"]));
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @return array
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getSubscribe(): array
    {
        $resources = array();
        if (isset($_GET["uid"])) {
            $result = $this->ce->getSubscribe($_GET["uid"], !empty($_GET["limit"]) ? (int)$_GET["limit"] : 10, !empty($_GET["skip"]) ? (int)$_GET["skip"] + 10 : 10);
            if (isset($result->subscribe)) {
                $resource_ids = array_reverse($result->subscribe);
                if ($resource_ids) {
                    foreach ($resource_ids as $resource_id) {
                        $resource = $this->ce->setResource("manga")->getResourceById($resource_id);
                        $resource->info = $this->scrapy->getElementById($resource->source, $resource->source_id);
                        $resource->thumb = $this->ic->getThumbInfo($resource->thumb_id);
                        $resources[] = $resource;
                    }
                }
            }
            return $resources;
        } else {
            throw new Exception("less necessary parameter!");
        }
    }

    /**
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getUserConfig()
    {
        if (isset($_GET["uid"])) {
            $result = $this->ce->user->findOne(array("uid" => (int)$_GET["uid"]), array("projection" => array("config" . (empty($_GET["key"]) ? "" : ".{$_GET["key"]}") => 1)));
            if (empty($result->config)) {
                return new \stdClass();
            } else {
                return $result->config;
            }
        } else {
            throw new Exception("less necessary parameter uid!");
        }
    }

    /**
     * @return array
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function setUserConfig()
    {
        if (!empty($_GET["uid"]) && !empty($_GET["key"]) && !empty($_GET["value"])) {
            return $this->ce->user->findAndModify(array("uid" => (int)$_GET["uid"]), array('$set' => array("config" . (empty($_GET["key"]) ? "" : ".{$_GET["key"]}") => json_decode($_GET["value"]))));
        } else {
            throw new Exception("less necessary parameter uid , key, value!");
        }
    }
}