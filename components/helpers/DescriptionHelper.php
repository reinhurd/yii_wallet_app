<?php

namespace app\components\helpers;

class DescriptionHelper
{
    public function getDescriptionFromArray(array $assocArray, $delimeter = ' => '): string
    {
        $result = '';
        foreach ($assocArray as $command => $description) {
            $result .= chr(10) . $command . $delimeter . $description;
        }

        return $result;
    }
}
