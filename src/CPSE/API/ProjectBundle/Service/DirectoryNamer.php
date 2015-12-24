<?php

namespace CPSE\API\ProjectBundle\Service;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use CPSE\API\ProjectBundle\Document\Document;
use CPSE\API\ProjectBundle\Document\Project;

use CPSE\API\ProjectBundle\Helpers\Helper as Helper;

class DirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping)
    {
        if ($object instanceof Document) {
            return Helper::codau2khongdau($this->getParentPath($object->getCategoryId()), true);
        } elseif ($object instanceof Project){
            return Helper::codau2khongdau($this->getParentPath($object->getCategory()) . $object->getName(), true);
        }
        return '';
    }
    
    protected function getParentPath($object){
        $path = $object->getName();
        while (null !== $object->getParent() && $object->getParent()->getName() != "") {
            $object = $object->getParent();
            $path = $object . '/' . $path;
        }
        return $path;
    }
    
    
}
