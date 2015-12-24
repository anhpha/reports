<?php

namespace CPSE\API\ProjectBundle\Service;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\OrignameNamer;
use CPSE\API\ProjectBundle\Helpers\Helper as Helper;

class FileNamer extends OrignameNamer
{
    public function name($object, PropertyMapping $mapping)
    {
        $name = parent::name($object, $mapping);
    
        /** @var $file UploadedFile */
        $nameArray = explode(".", $name);
        if (count($nameArray) < 2) {
            return Helper::codau2khongdau($name, true);
        }
        $extension = $nameArray[count($nameArray) -1];
        unset($nameArray[count($nameArray) -1]);
        $validName = Helper::codau2khongdau(implode(".", $nameArray), true);
        return $validName.".".$extension;
    }
}