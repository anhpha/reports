<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CPSE\API\ProjectBundle\Document\ProjectCategory;
use CPSE\API\ProjectBundle\Form\ProjectCategoryType;
use CPSE\API\ProjectBundle\Classes\KeyString;

/**
 * ProjectCategory controller.
 *
 * @Route("/projectcategory")
 */
class ProjectCategoryController extends Controller
{
    /**
     * Lists all ProjectCategory documents.
     *
     * @Route("/", name="projectcategory")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIProjectBundle:ProjectCategory')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIProjectBundle:ProjectCategory:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new ProjectCategory document.
     *
     * @Route("/new", name="projectcategory_new")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new ProjectCategory();
        $form = $this->createForm(new ProjectCategoryType(), $document);

        return $this->render('CPSEAPIProjectBundle:ProjectCategory:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new ProjectCategory document.
     *
     * @Route("/create", name="projectcategory_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new ProjectCategory();
        $form     = $this->createForm(new ProjectCategoryType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            //Blameable
            /* if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $document->setUpdatedBy($currentUser);
                $document->setCreatedBy($currentUser);
            } else {
                $document->setUpdatedBy(null);
                $document->setCreatedBy(null);
            } */
            
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('projectcategory_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:ProjectCategory:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a ProjectCategory document.
     *
     * @Route("/{id}", name="projectcategory_show")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:ProjectCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find ProjectCategory document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIProjectBundle:ProjectCategory:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing ProjectCategory document.
     *
     * @Route("/{id}/edit", name="projectcategory_edit")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:ProjectCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find ProjectCategory document.');
        }

        $editForm = $this->createForm(new ProjectCategoryType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:ProjectCategory:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing ProjectCategory document.
     *
     * @Route("/{id}/update", name="projectcategory_update")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:ProjectCategory')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find ProjectCategory document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new ProjectCategoryType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('projectcategory_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:ProjectCategory:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProjectCategory document.
     *
     * @Route("/{id}/delete", name="projectcategory_delete")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:ProjectCategory')->find($id);
            
            $projects = $dm->createQueryBuilder('CPSEAPIProjectBundle:Project')
                            ->field('category')->references($document)
                            ->getQuery()->execute();
            if ($projects && count($projects) > 0) {
                $translator = $this->get('translator');
                $errorMessage = $translator->trans(KeyString::PROJECTCATEGORY_CHILDRENEXISTED,
                                                    array(), 'projectcategory');
                throw $this->createAccessDeniedException($errorMessage);
            }
            if (!$document) {
                throw $this->createNotFoundException('Unable to find ProjectCategory document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('projectcategory'));
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
