<?php

namespace App\Enum;

abstract class BasicEnum {
    private static $_constants = NULL;
    
    public function __construct(){
              /*
        Preventing instance :)
              */
    }

    public static function getConstants() {
        if (self::$_constants == NULL) {
            self::$_constants = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$_constants)) {
            $reflect = new ReflectionClass($calledClass);
            self::$_constants[$calledClass] = $reflect->getConstants();
        }
        return self::$_constants[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
}