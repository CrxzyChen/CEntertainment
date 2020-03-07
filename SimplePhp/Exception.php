<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 13:30
 */

namespace SimplePhp;

class Exception extends \Exception
{
    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        echo "Error: " . $errno . ' ' . $errstr . ' ' . $errfile . ' ' . $errline . "<br>";
    }

    public static function exception_handler(\Throwable $e)
    {
        header("content-type:text/html");
        echo "<pre>";
        echo "Exception: " . $e->getCode() . ' ' . $e->getFile() . ' ' . $e->getLine() . '<br>';
        echo "  Message:" . $e->getMessage() . '<br>';
        if ($e->getTrace()) {
            echo "  Trace: " . '<br>';
            foreach ($e->getTrace() as $trace) {
                echo "      file:{$trace["file"]} line:{$trace["line"]} function: {$trace["class"]}{$trace["type"]}{$trace["function"]}()  args:" . json_encode($trace["args"]) . "<br>";
            };
        }
        exit;
    }

    /**
     * @param $class
     * @throws \ReflectionException
     */
    public static function autoload_register($class)
    {
        $class_relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        if (file_exists(LOCAL_ROOT . DIRECTORY_SEPARATOR . "$class_relative_path.php")) {
            require_once LOCAL_ROOT . DIRECTORY_SEPARATOR . "$class_relative_path.php";
        } else {
            throw new \ReflectionException();
        }
    }
}