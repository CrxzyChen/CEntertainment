<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:07
 */

namespace Models;

abstract class DBModel
{
    protected $connect;
    protected $driver;
    protected $db;

    /**
     * DBModel constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->onInitial();
        $this->setDriver();
        $this->setDatabase();
        $this->connect = new \SimplePhp\Database($this->getDriver());
        $this->onCreate();
    }

    abstract protected function onInitial();

    abstract protected function setDriver();

    abstract protected function onCreate();

    protected function setDatabase()
    {
    }

    private function getDriver(): string
    {
        return $this->driver;
    }

    public function __get($name)
    {
        $this->connect->Collection($name);
        return $this;
    }
}