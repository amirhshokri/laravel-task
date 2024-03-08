<?php

namespace App\Helpers;

class Helper
{
    public static function findPair(array $array, int $targetNumber): ?array
    {
        $arrayCount = count($array);

        for ($i = 0; $i < $arrayCount; $i++) {
            for ($j = $i+1; $j < $arrayCount; $j++) {
                if (($array[$i] + $array[$j]) > $targetNumber) {
                    return [$array[$i], $array[$j]];
                }
            }
        }

        return null;
    }
}
