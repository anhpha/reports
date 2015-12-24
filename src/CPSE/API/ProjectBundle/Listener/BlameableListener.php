<?php

namespace CPSE\API\ProjectBundle\Listener;

use Gedmo\Blameable\BlameableListener  as BaseListener;

class BlameableListener extends BaseListener {
    /**
     * Set a user value to return
     *
     * @param mixed $user
     */
    public function setUserValue($context)
    {
//         $token = $context->getToken();
//         if ($token) {
//             $this->user = $token->getUser();
//         } else {
//             $this->user = null;
//         }
        var_dump($context->get('security.token_storage'));
    }
}