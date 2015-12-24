<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CPSE\API\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author phavva
 * @Route("/")
 *
 */
class SecurityController extends BaseController
{

    /**
     * 
     * {@inheritDoc}
     * @see \FOS\UserBundle\Controller\SecurityController::loginAction()
     * @Route("/login", name="apiuser_login")
     */
    public function loginAction(Request $request)
    {
        return parent::loginAction($request);
    }
    
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('CPSEAPIUserBundle:APIUser:login.html.twig', $data);
    }

}
