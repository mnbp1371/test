<?php

namespace App\Helpers;

class Helper
{
    public static function convertToString(array &$data)
    {
        foreach ($data as $datumKey => $datumValue) {

            if (is_array($datumValue)) {
                self::convertToString($datumValue);
            }

            $data[$datumKey] = is_null($datumValue) ? '' : $datumValue;
        }
    }
}
