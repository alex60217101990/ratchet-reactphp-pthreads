<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.09.18
 * Time: 15:48
 */

namespace App\CustomClasses;

use App;

class ProtocClassesGenerator
{

    /**
     * @param string $class
     * @param string $method
     * @param array|null $values_arr
     * @return mixed
     */
    public function callNeededMethod(string $class, string $method, array $values_arr = null)
    {
        try {
            $reflectionMethod = new \ReflectionMethod($class, $method);
            if(count($values_arr) == 1)
                return $reflectionMethod->invoke(new $class(), $values_arr[0]);
            elseif (count($values_arr) > 1)
                return $reflectionMethod->invokeArgs(new $class(), $values_arr);
            else
                return $reflectionMethod->invoke(new $class(), null);
        }catch (\Exception $exception){
            return false;
        }
    }

    /**
     * @param string|null $class_name
     * @return string
     * @throws \ReflectionException
     */
    public function getCorrectClassName(?string $class_name)
    {
        $class = new \ReflectionClass($class_name);
        return $class->getName();
    }

    public function callNeededObject()
    {

    }

}