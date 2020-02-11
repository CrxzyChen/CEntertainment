<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:07
 */

namespace Models;

use SimplePhp\Exception;
use stdClass;

abstract class DBModel
{
    protected $connect;
    protected $config;
    protected $db;

    /**
     * DBModel constructor.
     * @throws Exception
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->onInitial();
        $this->connect = new \SimplePhp\Database($this->getConfig());
        $this->onCreate();
    }

    abstract protected function onInitial();
    abstract protected function onCreate();

    /**
     * @return stdClass
     * @throws Exception
     */
    private function getConfig():stdClass
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        try {
            $config = \SimplePhp\Config::get("db.$class[1]");
        } catch (Exception $e) {
            $config = \SimplePhp\Config::get("db.default");
        }
        return $config;
    }

    public function __get($name)
    {
        $this->connect->Collection($name);
        return $this;
    }
}