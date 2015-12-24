<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPSE\API\ProjectBundle\Document\DocumentStatus;
use CPSE\API\ProjectBundle\Form\DocumentStatusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DocumentStatus controller.
 *
 * @Route("/documentstatus")
 */
class DocumentStatusController extends Controller
{
    /**
     * Lists all DocumentStatus documents.
     *
     * @Route("/", name="documentstatus")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIProjectBundle:DocumentStatus')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIProjectBundle:DocumentStatus:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new DocumentStatus document.
     *
     * @Route("/new", name="documentstatus_new")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new DocumentStatus();
        $form = $this->createForm(new DocumentStatusType(), $document);

        return $this->render('CPSEAPIProjectBundle:DocumentStatus:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new DocumentStatus document.
     *
     * @Route("/create", name="documentstatus_create")
     * @Method("POST")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new DocumentStatus();
        $form     = $this->createForm(new DocumentStatusType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('documentstatus_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:DocumentStatus:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a DocumentStatus document.
     *
     * @Route("/{id}/show", name="documentstatus_show")
     * 
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentStatus')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentStatus document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIProjectBundle:DocumentStatus:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing DocumentStatus document.
     *
     * @Route("/{id}/edit", name="documentstatus_edit")
     * @Security("has_role('ROLE_DOC_EDITOR')")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentStatus')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentStatus document.');
        }

        $editForm = $this->createForm(new DocumentStatusType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:DocumentStatus:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing DocumentStatus document.
     *
     * @Route("/{id}/update", name="documentstatus_update")
     * @Method("POST")
     * @Security("has_role('ROLE_DOC_EDITOR')")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentStatus')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentStatus document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new DocumentStatusType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('documentstatus_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:DocumentStatus:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DocumentStatus document.
     *
     * @Route("/{id}/delete", name="documentstatus_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_DOC_EDITOR')")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentStatus')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find DocumentStatus document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('documentstatus'));
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
