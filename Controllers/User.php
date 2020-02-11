<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/11
 * Time: 20:49
 */

namespace Controllers;


use Models\CEntertainment;

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

}