<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/3/8
 * Time: 15:35
 */

namespace Models;


class ParamsChecker implements CheckerModel
{
    static private $error_set = array(
        101 => array("code" => 101, "message" => "miss necessary parameter!", "detail" => null),
        102 => array("code" => 102, "message" => "parameter form error!", "detail" => null),
    );
    private $last_error;

    public function judge($class, $method, $parameters): bool
    {
        foreach ($parameters as $parameter) {
            if ($parameter->default_value === null && $parameter->value === null) {
                $this->last_error = ParamsChecker::$error_set[101];
                $this->last_error['detail'] = array($parameter->name);
                return false;
            }
            if ($parameter->type != null) {
                if ($parameter->type == "array") {
                    if (!is_array(json_decode($_GET[$parameter->name], true))) {
                        $this->last_error = ParamsChecker::$error_set[102];
                        $this->last_error["detail"] = array("parameter" => $parameter->name, "error:" => "need a array");
                        return false;
                    }
                } else if ($parameter->type == "string") {
                    if (!is_string($_GET[$parameter->name])) {
                        $this->last_error = ParamsChecker::$error_set[102];
                        $this->last_error["detail"] = array("parameter" => $parameter->name, "error:" => "need a string");
                        return false;
                    }
                } else if ($parameter->type == "int") {
                    if (!is_numeric($_GET[$parameter->name])) {
                        $this->last_error = ParamsChecker::$error_set[102];
                        $this->last_error["detail"] = array("parameter" => $parameter->name, "error:" => "need a int");
                        return false;
                    }
                }
            }
        }
        return true;
        // TODO: Implement judge() method.
    }

    public function success()
    {
        // TODO: Implement success() method.
    }

    public function false()
    {
        return $this->last_error;
        // TODO: Implement false() method.
    }
}