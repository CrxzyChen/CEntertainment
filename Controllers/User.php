<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/11
 * Time: 20:49
 */

namespace Controllers;


use Models\CEntertainment;
use MongoDB\BSON\ObjectId;
use SimplePhp\Exception;

/**
 * Class User
 * @package Controllers
 * @property CEntertainment $ce
 */
class User extends ControllerBase
{

    /**
     * @throws \ReflectionException
     * @throws \SimplePhp\Exception
     */
    protected function onCreate()
    {
        $this->ce = new CEntertainment();
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
}