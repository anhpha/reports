<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CPSE\API\ProjectBundle\Document\Project;
use CPSE\API\ProjectBundle\Document\Document;
use CPSE\API\ProjectBundle\Form\ProjectType;
use CPSE\API\ProjectBundle\Classes\KeyString;

/**
 * Project controller.
 *
 * @Route("/projects")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project documents.
     *
     * @Route("/", name="project")
     *
     * @return array
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('CPSEAPIProjectBundle:Project')->findAll();
        
        $deleteForms = array();
        
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
        }

        return $this->render(
        'CPSEAPIProjectBundle:Project:index.html.twig', 
        array('documents' => $documents,'deleteForms'=>$deleteForms));
    }

    /**
     * Displays a form to create a new Project document.
     *
     * @Route("/new", name="project_new")
     *
     * @return array
     */
    public function newAction()
    {
        $document = new Project();
        $form = $this->createForm(new ProjectType(), $document);

        return $this->render('CPSEAPIProjectBundle:Project:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Creates a new Project document.
     *
     * @Route("/create", name="project_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new Project();
        $form     = $this->createForm(new ProjectType(), $document);
        $form->bind($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            
            //Create project folder
            $projectFolder = new Document(true);
            $projectFolder->setParent($document->getCategory()->getDocumentCategory());
            $projectFolder->setName($document->getName());
            $dm->persist($projectFolder);
            //Create references forlder
            $referencesFolder = new Document(true);
            $referencesFolder->setParent($projectFolder);
            $referencesFolder->setName(KeyString::PROJECT_FOLDER_REFERENCE);
            $document->setReferences($referencesFolder);
            //Create products folder
            $productsFolder = new Document(true);
            $productsFolder->setParent($projectFolder);
            $productsFolder->setName(KeyString::PROJECT_FOLDER_PRODUCT);
            $document->setProducts($productsFolder);
            
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:Project:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a Project document.
     *
     * @Route("/{id}/show", name="project_show")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $deleteForm = $this->createDeleteForm($id);


        return $this->render('CPSEAPIProjectBundle:Project:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),        ));
    }
    
    
    /**
     * Displays a form to edit an existing Project document.
     *
     * @Route("/{id}/edit", name="project_edit")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $editForm = $this->createForm(new ProjectType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:Project:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Project document.
     *
     * @Route("/{id}/update", name="project_update")
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

        $document = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Project document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new ProjectType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('project_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:Project:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Project document.
     *
     * @Route("/{id}/delete", name="project_delete")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Project document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('project'));
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
