<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPSE\API\ProjectBundle\Document\Status;
use CPSE\API\ProjectBundle\Form\StatusType;

/**
 * Status controller.
 *
 * @Route("/status")
 */
class StatusController extends Controller
{
    /**
     * Lists all Status documents.
     *
     * @Route("/", name="status")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIProjectBundle:Status')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIProjectBundle:Status:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new Status document.
     *
     * @Route("/new", name="status_new")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new Status();
        $form = $this->createForm(new StatusType(), $document);

        return $this->render('CPSEAPIProjectBundle:Status:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new Status document.
     *
     * @Route("/create", name="status_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new Status();
        $form     = $this->createForm(new StatusType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('status_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:Status:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a Status document.
     *
     * @Route("/{id}/show", name="status_show")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Status')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Status document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIProjectBundle:Status:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Status document.
     *
     * @Route("/{id}/edit", name="status_edit")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Status')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Status document.');
        }

        $editForm = $this->createForm(new StatusType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:Status:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Status document.
     *
     * @Route("/{id}/update", name="status_update")
     * @Method("POST")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Status')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Status document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new StatusType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('status_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:Status:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Status document.
     *
     * @Route("/{id}/delete", name="status_delete")
     * @Method("POST")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:Status')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Status document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('status'));
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
