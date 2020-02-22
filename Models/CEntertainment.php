<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/1
 * Time: 14:42
 */

namespace Models;

use Drivers\MongoDB;
use MongoDB\BSON\ObjectId;
use SimplePhp\Exception;

/**
 * @property MongoDB user
 * @property MongoDB centertainment_info
 * @property MongoDB resource
 */
class CEntertainment extends DBModel
{
    const USERNAME_CONFLICT = 11000;

    /**
     * @param $resource_id
     * @return mixed
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function upClickedCount(ObjectId $resource_id): int
    {
        $this->isSetResource();
        $result = $this->resource->findAndModify(array("_id" => new ObjectId($resource_id)), array('$inc' => array("clicked" => 1)));
        return isset($result[0]->clicked) ? $result[0]->clicked + 1 : 1;
    }

    /**
     * @param int $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addHistory(int $uid, ObjectId $resource_id): bool
    {
        $datetime = date('Y-m-d h:i:s', time());
        $this->user->findAndModify(array("uid" => $uid), array("\$push" => array("history" => array("datetime" => $datetime, "resource_id" => $resource_id))));
        return true;
    }

    /**
     * @param $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addLike(int $uid, ObjectId $resource_id): bool
    {
        $this->user->findAndModify(array("uid" => $uid), array("\$addToSet" => array("like" => $resource_id)));
        return true;
    }

    /**
     * @param int $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function isLike(int $uid, ObjectId $resource_id): bool
    {
        $resource = $this->user->count(array("uid" => $uid, "like" => array('$in' => array($resource_id))));
        if ($resource == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function removeLike(int $uid, ObjectId $resource_id): bool
    {
        $this->user->findAndModify(array("uid" => $uid), array("\$pull" => array("like" => $resource_id)));
        return true;
    }

    /**
     * @param $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function addSubscribe(int $uid, ObjectId $resource_id): bool
    {
        $this->user->findAndModify(array("uid" => $uid), array("\$addToSet" => array("subscribe" => $resource_id)));
        return true;
    }

    /**
     * @param int $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function removeSubscribe(int $uid, ObjectId $resource_id): bool
    {
        $this->user->findAndModify(array("uid" => $uid), array("\$pull" => array("subscribe" => $resource_id)));
        return true;
    }

    /**
     * @param int $uid
     * @param ObjectId $resource_id
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function isSubscribe(int $uid, ObjectId $resource_id): bool
    {
        $resource = $this->user->count(array("uid" => $uid, "subscribe" => array('$in' => array($resource_id))));
        if ($resource == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param ObjectId $resource_id
     * @return mixed
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getResourceById(ObjectId $resource_id)
    {
        $this->isSetResource();
        return $this->resource->findOne(array("_id" => $resource_id));
    }

    /**
     * @param int $uid
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getSubscribe(int $uid)
    {
        return $this->user->findOne(array("uid" => $uid), array("projection" => array("subscribe" => 1, "_id" => 0)));
    }

    protected function onCreate()
    {
        $this->centertainment_info = $this->connect->Collection("centertainment_info");
    }

    /**
     * @param string $resource
     * @return CEntertainment
     */
    public function setResource(string $resource): CEntertainment
    {
        $this->resource = $this->connect->Collection("{$resource}_resource");
        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     * @throws Exception
     */
    public function addUser($username, $password)
    {
        try {
            $uid = $this->getUserId();
            if ($this->user->count()) {
                $this->user->createIndexes(array("username" => 1), array("unique" => true));
            }
            $this->user->insert(array(
                "uid" => $uid,
                "username" => $username,
                "password" => md5($password),
                "detail_info" => array(),
                "history" => array(),
                "like" => array()
            ));
            return $uid;
        } catch (\Exception $e) {
            if ($e->getCode() == self::USERNAME_CONFLICT) {
                throw new Exception("username conflict!");
            }
        }
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return mixed
     * @throws Exception
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getLatest($limit = 10, $skip = 0)
    {
        $this->isSetResource();
        return $this->resource->find(array("recommend" => array('$ne' => null)), array("limit" => $limit, "skip" => $skip, "sort" => array("thumb_id" => -1)));
    }

    /**
     * @throws Exception
     */
    private function isSetResource()
    {
        if (!$this->resource) {
            throw new Exception("Resource is not appointed!");
        }
    }

    /**
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     */
    private function getUserId()
    {
        if (!$this->centertainment_info->findOne(array("info" => "user"))) {
            $this->centertainment_info->insert(array("info" => "user", "last_uid" => 1));
            return (1);
        } else {
            $info = $this->centertainment_info->findAndModify(array("info" => "user"), array('$inc' => array("last_uid" => 1)));
            $info = $info[0];
            return $info->last_uid + 1;
        }
    }

    protected function setDatabase(&$database)
    {
        $database = "centertainment";
        // TODO: Implement setDatabase() method.
    }
}