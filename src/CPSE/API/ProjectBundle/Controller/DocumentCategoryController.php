<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPSE\API\ProjectBundle\Document\DocumentCategory;
use CPSE\API\ProjectBundle\Form\DocumentCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DocumentCategory controller.
 *
 * @Route("/documentcategory")
 */
class DocumentCategoryController extends Controller
{
    /**
     * Lists all DocumentCategory documents.
     *
     * @Route("/", name="documentcategory")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIProjectBundle:DocumentCategory')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIProjectBundle:DocumentCategory:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new DocumentCategory document.
     *
     * @Route("/new", name="documentcategory_new")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new DocumentCategory();
        $form = $this->createForm(new DocumentCategoryType(), $document);

        return $this->render('CPSEAPIProjectBundle:DocumentCategory:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new DocumentCategory document.
     *
     * @Route("/create", name="documentcategory_create")
     * @Method("POST")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new DocumentCategory();
        $form     = $this->createForm(new DocumentCategoryType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $document->setUpdatedBy($currentUser);
                $document->setCreatedBy($currentUser);
            } else {
                $document->setUpdatedBy(null);
                $document->setCreatedBy(null);
            }
            
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('documentcategory_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:DocumentCategory:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a DocumentCategory document.
     *
     * @Route("/{id}/show", name="documentcategory_show")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentCategory document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIProjectBundle:DocumentCategory:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing DocumentCategory document.
     *
     * @Route("/{id}/edit", name="documentcategory_edit")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentCategory document.');
        }

        $editForm = $this->createForm(new DocumentCategoryType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:DocumentCategory:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing DocumentCategory document.
     *
     * @Route("/{id}/update", name="documentcategory_update")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find DocumentCategory document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new DocumentCategoryType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $document->setUpdatedBy($currentUser);
            } else {
                $document->setUpdatedBy(null);
            }
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('documentcategory_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:DocumentCategory:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DocumentCategory document.
     *
     * @Route("/{id}/delete", name="documentcategory_delete")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:DocumentCategory')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find DocumentCategory document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('documentcategory'));
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
