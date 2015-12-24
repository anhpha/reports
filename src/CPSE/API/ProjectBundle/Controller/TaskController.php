<?php

namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CPSE\API\ProjectBundle\Document\Task;
use CPSE\API\ProjectBundle\Document\TaskComment;
use CPSE\API\ProjectBundle\Form\TaskType;
use CPSE\API\ProjectBundle\Classes\KeyString;
use CPSE\API\ProjectBundle\Form\DocumentSimpleType;


/**
 * Task controller.
 *
 * @Route("projects/{projectId}/tasks")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task documents.
     *
     * @Route("/", name="task")
     *
     * @return array
     */
    public function indexAction($projectId)
    {
        $dm = $this->getDocumentManager();
        $project = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($projectId);
        if (! $project) {
            throw $this->createNotFoundException('Unable to find project with requested id');
        }
        $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Task');
        
        $documents = $qb->field('project')->references($project)->getQuery()->execute();
        
        $timeSpent = array();
        $deleteForms = array();
        $now = new \DateTime();
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())->createView();
            //calculate percent of time by compare now to total time
            $fullTime = $document->getTo()->diff($document->getFrom())->d;
            $spent = $now->diff($document->getFrom())->d;
            $percent = ($spent*100)/$fullTime;
            $timeSpent[$document->getId()] = $percent;
        }
        
        $translator = $this->get('translator');        
        $statusArray = array(
                    Task::CREATED => $translator->trans(KeyString::TASK_CREATED, array(), 'task'),
                    Task::ASSIGNED => $translator->trans(KeyString::TASK_ASSIGNED, array(), 'task'),
                    Task::PENDING => $translator->trans(KeyString::TASK_PENDING, array(), 'task'),
                    Task::FINISHED => $translator->trans(KeyString::TASK_FINISHED, array(), 'task'),
        );

        return $this->render(
        'CPSEAPIProjectBundle:Task:index.html.twig', 
        array('documents' => $documents,
              'deleteForms'=>$deleteForms,
              'statusArray' => $statusArray,
              'timeSpent' => $timeSpent,
              'project' => $project
        ));
    }

    /**
     * Displays a form to create a new Task document.
     *
     * @Route("/new", name="task_new")
     *
     * @return array
     */
    public function newAction(Request $request, $projectId)
    {
        $dm = $this->getDocumentManager();
        $project = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($projectId);
        if (! $project) {
            throw $this->createNotFoundException('Unable to find project with requested id');
        }
        $document = new Task();
        
        $form = $this->createForm(new TaskType(), $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Blameable
            $user = null;
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $user = $currentUser;
            }
            $document->setUpdatedBy($user);
            $document->setCreatedBy($user);
            for ($i=0; $i < count($document->getRelatedDocs()); $i++) {
                if (array_key_exists($i, $document->getRelatedDocs())) {
                    $document->getRelatedDocs()[$i]->setCreatedBy($user);
                    $document->getRelatedDocs()[$i]->setUpdatedBy($user);
                }
            }
            $document->setProject($project);
            $dm->persist($document);
            $dm->flush();
        
            return $this->redirect($this->generateUrl('task_show', array(
                                        'projectId' => $projectId,
                                        'id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:Task:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView(),
            'projectId' => $projectId
        ));
    }

    /**
     * Creates a new Task document.
     *
     * @Route("/create", name="task_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $document = new Task();
        $form     = $this->createForm(new TaskType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //Blameable
            $user = null;
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $user = $currentUser;
            } 
            $document->setUpdatedBy($user);
            $document->setCreatedBy($user);
            for ($i=0; $i < count($document->getRelatedDocs()); $i++) {
                if (array_key_exists($i, $document->getRelatedDocs())) {
                    $document->getRelatedDocs()[$i]->setCreatedBy($user);
                    $document->getRelatedDocs()[$i]->setUpdatedBy($user);
                }
            }
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('task_show', array('id' => $document->getId())));
        }

        return $this->render('CPSEAPIProjectBundle:Task:new.html.twig', array(
            'document' => $document,
            'form'     => $form->createView()
        ));
    }

    /**
     * Finds and displays a Task document.
     *
     * @Route("/{id}/show", name="task_show")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function showAction($id, $projectId)
    {
        return $this->showTaskInfor($id, $projectId, 'CPSEAPIProjectBundle:Task:show.html.twig');
    }
    
    /**
     * Finds and displays a Task document.
     *
     * @Route("/{id}/detail", name="task_detail")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function showDetailAction($id, $projectId)
    {
        return $this->showTaskInfor($id, $projectId, 'CPSEAPIProjectBundle:Task:detail.html.twig');
    }
    
    private function showTaskInfor($id, $projectId, $viewTemplate)
    {
        $dm = $this->getDocumentManager();
        
        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);
        
        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $translator = $this->get('translator');
        $statusArray = array(
            Task::CREATED => $translator->trans(KeyString::TASK_CREATED, array(), 'task'),
            Task::ASSIGNED => $translator->trans(KeyString::TASK_ASSIGNED, array(), 'task'),
            Task::PENDING => $translator->trans(KeyString::TASK_PENDING, array(), 'task'),
            Task::FINISHED => $translator->trans(KeyString::TASK_FINISHED, array(), 'task'),
        );
        
        return $this->render($viewTemplate, array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),
            'statusArray' =>$statusArray,
            'projectId' => $projectId
        ));
    }

    /**
     * Displays a form to edit an existing Task document.
     *
     * @Route("/{id}/edit", name="task_edit")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction($id, $projectId)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }

        $editForm = $this->createForm(new TaskType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CPSEAPIProjectBundle:Task:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'projectId' => $projectId
        ));
    }

    /**
     * Edits an existing Task document.
     *
     * @Route("/{id}/update", name="task_update")
     * @Method("POST")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function updateAction(Request $request, $id, $projectId)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new TaskType(), $document);
        $editForm->bind($request);

        if ($editForm->isValid()) {
        //Blameable
            $user = null;
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $user = $currentUser;
            } 
            $document->setUpdatedBy($user);
            for ($i=0; $i < count($document->getRelatedDocs()); $i++) {
                if (array_key_exists($i, $document->getRelatedDocs())) {
                    $document->getRelatedDocs()[$i]->setUpdatedBy($user);
                }
            }
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('task_edit', array('id' => $id)));
        }

        return $this->render('CPSEAPIProjectBundle:Task:edit.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'projectId' => $projectId
        ));
    }
    
    
    /**
     * Approve a Task document.
     *
     * @Route("/{id}/approve", name="task_approve")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function approveAction(Request $request, $id, $projectId)
    {
        return $this->doApproval($request, $id, $projectId);

    }
    
    /**
     * Approve a Task document.
     *
     * @Route("/{id}/reject", name="task_reject")
     *
     * @param Request $request The request object
     * @param string $id       The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function rejectAction(Request $request, $id, $projectId)
    {
        return $this->doApproval($request, $id, $projectId, false);
    
    }
    
    /**
     * Conduct approve or reject action
     */
     private function doApproval($request, $id, $projectId, $approval = true)
     {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }
        $newComment = $this->initComment($document);
        $document->addComment($newComment);
        $editForm = $this->createForm(new TaskType(), $document);
        $editForm = $this->createAprroveFrom($editForm);
        $editForm->handleRequest($request);
    
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($approval == true) {
                $document->setStatus(Task::FINISHED);
                //update finished date
                $document->setFinishedOn(new \DateTime());
            } else {
                $document->setStatus(Task::ASSIGNED);
            }
            //Set comment data
            $numberComments = count($document->getComments());
            $statusTextKeys = array(KeyString::TASK_CREATED, KeyString::TASK_ASSIGNED,
                KeyString::TASK_PENDING, KeyString::TASK_FINISHED
            );
            //Set action text for log only
            $translator = $this->get('translator');
            $document->getComments()[$numberComments-1]->setAction(
                $document->getComments()[$numberComments-1]->getAction(). ' "'.
                $translator->trans($statusTextKeys[$document->getStatus()], array(), 'task') . '"');
            
            //Blameable
            $user = null;
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $user = $currentUser;
            }
            $document->setUpdatedBy($user);
            for ($i=0; $i < count($document->getProducts()); $i++) {
                if (array_key_exists($i, $document->getProducts())) {
                    $document->getProducts()[$i]->setCreatedBy($user);
                }
            }
            $dm->persist($document);
            $dm->flush();
            return $this->redirect($this->generateUrl('task_show', 
                array('id' => $document->getId(),'projectId' => $projectId
            )));
        }
        return $this->render('CPSEAPIProjectBundle:Task:approve.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'approval' => $approval,
            'projectId' => $projectId
        ));
     }

    
    /**
     * Create form for approval
     * @param unknown $editForm
     */
    private function createAprroveFrom($editForm)
    {
        return $editForm
        ->remove('desription')
        ->remove('from')
        ->remove('to')
        ->remove('assigned_to')
        ->remove('related_docs')
        ->add('comments', 'collection', array('type' => new \CPSE\API\ProjectBundle\Form\TaskCommentType(),
            'allow_add'    => true,
            'required' => false
        ))
        ;
    }
    /**
     * Deletes a Task document.
     *
     * @Route("/{id}/delete", name="task_delete")
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Task document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('task'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function createChangeStatusFrom($editForm)
    {
        return $editForm
        ->remove('desription')
        ->remove('from')
        ->remove('to')
        ->remove('assigned_to')
        ->remove('related_docs')
        ->add('status', 'choice', array(
                'choices' => array(
                    Task::ASSIGNED => KeyString::TASK_ASSIGNED,
                    Task::PENDING => KeyString::TASK_PENDING,
                ),
                'required' => true,
                'choice_translation_domain' => 'task'
            ))
         ->add('products', 'collection', array(
                        'type' => new DocumentSimpleType(),
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'by_reference' => false,
            ))
         ->add('comments', 'collection', array('type' => new \CPSE\API\ProjectBundle\Form\TaskCommentSimpleType(),
                                                'allow_add'    => true
         ))
        ;
    }
    
    /**
     * 
     * @Route("/{id}/updatestatus", name="task_updatestatus")
     * @Method("POST")
     */
    public function updateStatusAction(Request $request, $id, $projectId){
        $dm = $this->getDocumentManager();
        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);
        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }
        $changeStatusFrom  = $this->createForm(new TaskType(), $document);
        $changeStatusFrom = $this->createChangeStatusFrom($changeStatusFrom);
        $changeStatusFrom->bind($request);
        if ($changeStatusFrom->isValid()) {
            $numberComments = count($document->getComments());
            $statusTextKeys = array(KeyString::TASK_CREATED, KeyString::TASK_ASSIGNED,
                KeyString::TASK_PENDING, KeyString::TASK_FINISHED
            );
            $translator = $this->get('translator');
            $document->getComments()[$numberComments-1]->setAction(
                $document->getComments()[$numberComments-1]->getAction(). ' "'.
             $translator->trans($statusTextKeys[$document->getStatus()], array(), 'task') . '"');
            //update finished date
            if ($document->getStatus() == Task::FINISHED) {
                $document->setFinishedOn(new \DateTime());
            } else {
                $document->setFinishedOn(null);
            }
            //Blameable
            $user = null;
            if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $currentUser = $this->get('security.token_storage')
                ->getToken()
                ->getUser();
                $user = $currentUser;
            }
            $document->setUpdatedBy($user);
            for ($i=0; $i < count($document->getProducts()); $i++) {
                if (array_key_exists($i, $document->getProducts())) {
                    $document->getProducts()[$i]->setCreatedBy($user);
                }
            }
            $dm->persist($document);
            $dm->flush();
            return $this->redirect($this->generateUrl('task', array('projectId' => $projectId)));
        }
        
        return $this->render('CPSEAPIProjectBundle:Task:changestatus.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'projectId' => $projectId
        ));
    }
    
    /**
     * Displays a form to edit an existing Task document.
     *
     * @Route("/{id}/changestatus", name="task_changestatus")
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function changeStatusAction($id, $projectId)
    {
        $dm = $this->getDocumentManager();
    
        $document = $dm->getRepository('CPSEAPIProjectBundle:Task')->find($id);
    
        if (!$document) {
            throw $this->createNotFoundException('Unable to find Task document.');
        }
        
        $newComment = $this->initComment($document);
        /* $newComment->setCreatedAt(new \DateTime());
        $statusTextKeys = array(KeyString::TASK_CREATED, KeyString::TASK_ASSIGNED,
                                KeyString::TASK_PENDING, KeyString::TASK_FINISHED
        );
        $translator = $this->get('translator');
        $newComment->setAction($translator->trans(KeyString::STATUS_CHANGE_PREFIX, array(), 'task').
            ' "' . $translator->trans($statusTextKeys[$document->getStatus()], array(), 'task') . '" '.
            $translator->trans(KeyString::STATUS_CHANGE_TO, array(), 'task')); */
        $document->addComment($newComment);
        $numberComments = count($document->getComments());
    
        $editForm = $this->createForm(new TaskType(), $document);
        $editForm = $this->createChangeStatusFrom($editForm);
    
        return $this->render('CPSEAPIProjectBundle:Task:changestatus.html.twig', array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'projectId' => $projectId
        ));
    }
    
    /**
     * Init a new comment
     * @return \CPSE\API\ProjectBundle\Document\TaskComment
     */
    private function initComment($document){
        $newComment = new TaskComment();
        $newComment->setCreatedAt(new \DateTime());
        $statusTextKeys = array(KeyString::TASK_CREATED, KeyString::TASK_ASSIGNED,
            KeyString::TASK_PENDING, KeyString::TASK_FINISHED
        );
        $translator = $this->get('translator');
        $newComment->setAction($translator->trans(KeyString::STATUS_CHANGE_PREFIX, array(), 'task').
            ' "' . $translator->trans($statusTextKeys[$document->getStatus()], array(), 'task') . '" '.
            $translator->trans(KeyString::STATUS_CHANGE_TO, array(), 'task'));
        return $newComment;
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
