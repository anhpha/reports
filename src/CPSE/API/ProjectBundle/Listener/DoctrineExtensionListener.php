<?php

namespace CPSE\API\ProjectBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->container != null && $this->container->get('security.token_storage') != null
            && $currentUser = $this->container->get('security.token_storage')->getToken() != null) {
             $currentUser = $this->container->get('security.token_storage')
            ->getToken()
            ->getUser();
            $blameable = $this->container->get('gedmo.listener.Blameable');
            $blameable->setUserValue($currentUser); 
        } 
    }
}