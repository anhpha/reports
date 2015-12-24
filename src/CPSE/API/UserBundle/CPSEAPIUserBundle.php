<?php

namespace CPSE\API\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CPSEAPIUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
