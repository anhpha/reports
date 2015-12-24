<?php

namespace CPSE\API\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPSE\API\UserBundle\Document\APIUser;
use CPSE\API\UserBundle\Form\APIUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * APIUser controller.
 *
 * @Route("/apiuser")
 */
class APIUserController extends Controller
{
    /**
     * Lists all APIUser documents.
     *
     * @Route("/", name="apiuser")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIUserBundle:APIUser')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIUserBundle:APIUser:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new APIUser document.
     *
     * @Route("/new", name="apiuser_new")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new APIUser();
        $form = $this->createForm(new APIUserType(), $document);
        $form->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'apiuser'),
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Retype password'),
            'invalid_message' => 'Passwords do not match!',
        ));
        $form->add('username');

        return $this->render('CPSEAPIUserBundle:APIUser:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new APIUser document.
     *
     * @Route("/create", name="apiuser_create")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new APIUser();
        $form     = $this->createForm(new APIUserType(), $document);
        $form->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'apiuser'),
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Retype password'),
            'invalid_message' => 'Passwords do not match!',
        ));
        $form->add('username');
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('apiuser_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIUserBundle:APIUser:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a APIUser document.
     *
     * @Route("/{id}/show", name="apiuser_show")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIUserBundle:APIUser')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find APIUser document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIUserBundle:APIUser:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing APIUser document.
     *
     * @Route("/{id}/edit", name="apiuser_edit")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIUserBundle:APIUser')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find APIUser document.');
        }

        $editForm = $this->createForm(new APIUserType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIUserBundle:APIUser:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing APIUser document.
     *
     * @Route("/{id}/update", name="apiuser_update")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function updateAction(Request $request, $id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIUserBundle:APIUser')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find APIUser document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new APIUserType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('apiuser_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIUserBundle:APIUser:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a APIUser document.
     *
     * @Route("/{id}/delete", name="apiuser_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $document = $dm->getRepository('CPSEAPIUserBundle:APIUser')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find APIUser document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('apiuser'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
