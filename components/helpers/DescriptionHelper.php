<?php

namespace app\components\helpers;

class DescriptionHelper
{
    public function getDescriptionFromArray(array $assocArray, $delimeter = ' => '): string
    {
        $result = '';
        foreach ($assocArray as $command => $description) {
            $result = "\n" . $command . $delimeter . $description;
        }

        return urlencode($result);
    }
}
