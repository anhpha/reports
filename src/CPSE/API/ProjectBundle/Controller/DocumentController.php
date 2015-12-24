<?php
namespace CPSE\API\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CPSE\API\ProjectBundle\Document\Document;
use CPSE\API\ProjectBundle\Form\DocumentType;
use CPSE\API\ProjectBundle\Form\DocumentSearchQueryType;
use CPSE\API\ProjectBundle\Classes\DocumentSearchQuery;
use CPSE\API\ProjectBundle\Classes\KeyString;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Document controller.
 *
 */
class DocumentController extends FOSRestController
{
    
    /**
     * Lists all Document documents.
     *
     * @Route("/document/{title}-{page}", name="document",
     * defaults={"page" = 1, "title"="page"},
     * requirements={"page": "\d+", "title" : "page"})
     *
     * @return array
     */
    public function indexAction($page, Request $request)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params, 'CPSEAPIProjectBundle:Document:index.html.twig', Document::$pageSize, $page);
    }

    /**
     * @Route("/document/all", name="doument_all")
     */
    public function allAction(Request $request)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params, 'CPSEAPIProjectBundle:Document:index.html.twig');
    }

    /**
     * Render list of documents based on params
     *
     * @param array $params
     *            query parameters
     * @param string $template
     *            name of template to render
     * @param integer $pageSize
     *            size of page for pagination
     * @param integer $currentPage            
     */
    private function showAllDocuments($params, $template, $pageSize = null, 
        $currentPage = 1, $catId = null, $fileOnly = true, $folderOnly = false, 
        $currenCatOnly = false, $others = array())
    {
        // Set default sort
        $sortedBy = "updatedAt";
        $order = "desc";
        // Sort as request
        if (isset($params['sortedBy']) && $params['sortedBy'] != '') {
            $sortedBy = $params['sortedBy'];
        }
        if (isset($params['order']) && $params['order'] != '') {
            $order = $params['order'];
        }
        $dm = $this->getDocumentManager();
        
        if (!empty($catId)){
            $category = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
            if (! $category) {
                throw $this->createNotFoundException('Unable to find category');
            }
        }
        // count
        $totalQb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document');
        if ($fileOnly) {
            $totalQb->field('object_type')->equals(0);
        }
        if ($folderOnly) {
            $totalQb->field('object_type')->equals(1);
        }
        if (!empty($catId) && $category) {
            $totalQb->field('parent')->references($category);
            //If only show child of this cat => show all child of root category
        } else if($currenCatOnly) {
            $totalQb->field('parent')->equals(null);
        }
            
        $total = $totalQb->count()->getQuery()->execute();
        $limit = isset($pageSize) ? $pageSize : $total;
        // Retrive documents
        $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document')
            ->sort($sortedBy, $order)
            ->limit($limit)
            ->skip(($currentPage - 1) * Document::$pageSize);
        if ($fileOnly) {
            $qb->field('object_type')->equals(Document::FILE);
        }
        if ($folderOnly) {
            $qb->field('object_type')->equals(Document::FOLDER);
        }
        if (!empty($catId) && $category) {
            $qb->field('parent')->references($category);
        } else if($currenCatOnly) {
            $qb->field('parent')->equals(null);
        }
        $documents = $qb->getQuery()->execute();
        
        // Create delete form for management
        $deleteForms = array();
        foreach ($documents as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())
                ->createView();
        }
        // Render results
        $formParams = array(
            'documents' => $documents,
            'deleteForms' => $deleteForms,
            'total' => $total,
            'currentPage' => $currentPage,
            'pageSize' => $limit,
            'currentSort' => isset($params['sortedBy']) ? $params['sortedBy'] : null,
            'currentOrder' => isset($params['order']) ? $params['order'] : null,
            'currentCategory' => isset($category)? $category: null
        );
        $formParams = array_merge($formParams, $others);
        return $this->render($template, $formParams);
    }

    /**
     * Displays a form to create a new Document document.
     * @Security("has_role('ROLE_DOC_EDITOR')")
     * @Route("/documents/new", name="document_new")
     *
     * @return array
     */
    public function newAction(Request $request)
    {
        $document = new Document();
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        $catId = '';
        if (isset($params['catId']) && $params['catId'] != '') {
            $catId = $params['catId'];
        }
        if ($catId != '') {
            $dm = $this->getDocumentManager();
            $cat = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
            if (! $cat) {
                throw $this->createNotFoundException('Unable to find category' . $catId);
            }
            $document->setParent($cat);
        } 
        $form = $this->createForm(new DocumentType(), $document);
        
        return $this->render('CPSEAPIProjectBundle:Document:new.html.twig', array(
            'document' => $document,
            'form' => $form->createView(),
            'currentCategory' => $document->getParent(),
            'returnPath' => isset($params['returnPath'])?$params['returnPath']: null
        ));
    }
    
    /**
     * Displays a form to create a new Document document.
     * @Security("has_role('ROLE_DOC_EDITOR')")
     * @Route("/document/newfolder", name="document_folder_new")
     *
     * @return array
     */
    public function newFolderAction(Request $request)
    {
        $document = new Document(true);
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        $catId = '';
        if (isset($params['catId']) && $params['catId'] != '') {
            $catId = $params['catId'];
        }
        if ($catId != '') {
            $dm = $this->getDocumentManager();
            $cat = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
            if (! $cat) {
                throw $this->createNotFoundException('Unable to find category' . $catId);
            }
            $document->setParent($cat);
        }
        $form = $this->createForm(new DocumentType(), $document);
        //For folder, it does not need file and it's parent is optional
        $form->remove('file')
            ->remove('type');
        
        //set to optional
        $form->add('type', 'hidden', array('data' => Document::FOLDER))
            ;
        
    
        return $this->render('CPSEAPIProjectBundle:Document:new.html.twig', array(
            'document' => $document,
            'form' => $form->createView(),
            'currentCategory' => $document->getParent(),
            'returnPath' => isset($params['returnPath'])?$params['returnPath']: null
        ));
    }
    
    
    /**
     * Creates a new Document document.
     * @Security("has_role('ROLE_DOC_EDITOR')")
     * @Route("/document/create", name="document_create")
     * @Method("POST")
     *
     * @param Request $request            
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        $document = new Document();
        $form = $this->createForm(new DocumentType(), $document);
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
            if ($document->getType() == Document::FOLDER){
                $document->setFileType('Folder');
            }
            
            $params = array();
            $params = array_merge($request->request->all(), $request->query->all());
            $catId = '';
            if (isset($params['catId']) && $params['catId'] != '') {
                $catId = $params['catId'];
            }
            if ($catId != '') {
                $dm = $this->getDocumentManager();
                $cat = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
                if (! $cat) {
                    throw $this->createNotFoundException('Unable to find category' . $catId);
                }
                $document->setParent($cat);
            }
            
            
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();
            if (!empty($params['returnPath'])) {
                return $this->redirect($params['returnPath']);
            }
            return $this->redirect($this->generateUrl('document_view_category', array('categoryId' => $catId)));
        }
        
        return $this->render('CPSEAPIProjectBundle:Document:new.html.twig', array(
            'document' => $document,
            'form' => $form->createView(),
            'projectId' => $params['projectId'],
            'returnPath' => isset($params['returnPath'])?$params['returnPath']: null
        ));
    }


    /**
     * Displays a form to edit an existing Document document.
     * @Route("/document/{id}/edit", name="document_edit")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @param string $id
     *            The document ID
     *            
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction(Request $request, $id)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        $dm = $this->getDocumentManager();
        
        $document = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($id);
        
        if (! $document) {
            throw $this->createNotFoundException('Unable to find Document document.');
        }
        
        
        $editForm = $this->createForm(new DocumentType(), $document);
        
        if ($document->getType() == Document::FOLDER) {
            $editForm->remove('file');
        }
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('CPSEAPIProjectBundle:Document:edit.html.twig', array(
            'document' => $document,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'returnPath' => isset($params['returnPath'])?$params['returnPath']: null
        ));
    }
    

    /**
     * Edits an existing Document document.
     * @Route("/document/{id}/update", name="document_update")
     * @Method("POST")
     * @Security("has_role('ROLE_DOC_EDITOR')")
     *
     * @param Request $request
     *            The request object
     * @param string $id
     *            The document ID
     *            
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function updateAction(Request $request, $id)
    {
        $dm = $this->getDocumentManager();
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        $catId = '';
        if (isset($params['catId']) && $params['catId'] != '') {
            $catId = $params['catId'];
        }
        $document = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($id);
        
        if (! $document) {
            throw $this->createNotFoundException('Unable to find Document document.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentType(), $document);
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
            
            if (!empty($params['returnPath'])) {
                return $this->redirect($params['returnPath']);
            }
            return $this->redirect($this->generateUrl('document_view_category', array('categoryId' => $catId)));
        }
        
        return $this->render('CPSEAPIProjectBundle:Document:edit.html.twig', array(
            'document' => $document,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'returnPath' => isset($params['returnPath'])?$params['returnPath']: null
        ));
    }

    /**
     * Deletes a Document document.
     * @Route("/document/{id}/delete", name="document_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *            The request object
     * @param string $id
     *            The document ID
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
            $document = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($id);
            $traslator = $this->get('translator');
            if (! $document) {
                throw $this->createNotFoundException(
                    $traslator->trans(KeyString::DOCUMENT_NOTFOUND, array(), 'document'));
            }
            
            if ($document->getOwner() != null){
                throw $this->createAccessDeniedException(
                    $traslator->trans(KeyString::DOCUMENT_BEINGUSED, array(), 'document'));
            }
            
            //Renive all children
            $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document')
                        ->remove()
                        ->field('parent')->references($document)
                        ->getQuery()
                        ->execute();
            
            $dm->remove($document);
            $dm->flush();
        }
        
        return $this->redirect($this->generateUrl('document_view_category'));
    }

    /**
     * @Route("/documents/search/{title}-{currentPage}", name="document_search",
     * defaults={"currentPage" = 1, "title"="page"},
     * requirements={"currentPage": "\d+", "title" : "page"})
     *
     * @param Request $request            
     */
    public function searchAction(Request $request, $currentPage)
    {
        $query = new DocumentSearchQuery();
        $searchForm = $this->createForm(new DocumentSearchQueryType(), $query);
        $searchForm->handleRequest($request);
        $query->setCurrentPage($currentPage);
        
        $results = array();
        $sortedBy = ! empty($query->getSortedBy()) ? $query->getSortedBy() : 'name';
        $order = ! empty($query->getOrder()) ? $query->getOrder() : 'asc';
        
        $dm = $this->getDocumentManager();
        $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document');
        $expr = $qb->expr()->operator('$text', array(
            '$search' => $query->getQ()
        ));
        $qb->field(null)->equals($expr->getQuery());
        
        $contQb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document');
        $contQb->field(null)->equals($expr->getQuery());
        $total = $contQb->count()
            ->getQuery()
            ->execute();
        
        $mongo = $qb->sort($sortedBy, $order)
            ->limit(Document::$pageSize)
            ->skip(($currentPage - 1) * Document::$pageSize)
            ->getQuery();
        $results = $mongo->execute();
        // Create delete form for management
        $deleteForms = array();
        foreach ($results as $document) {
            $deleteForms[$document->getId()] = $this->createDeleteForm($document->getId())
                ->createView();
        }
        
        // Render results
        return $this->render('CPSEAPIProjectBundle:Document:search.html.twig', array(
            'documents' => $results,
            'q' => $query->getQ(),
            'deleteForms' => $deleteForms,
            'total' => $total,
            'currentPage' => $currentPage,
            'pageSize' => Document::$pageSize,
            'currentSort' => $query->getSortedBy(),
            'currentOrder' => $query->getOrder()
        ));
        /*
         * return $this->render('CPSEAPIProjectBundle:Document:search.html.twig', array(
         * 'documents' => $results,
         * 'q' => $query->getQ()
         * ));
         */
    }
    
    /**
     * 
     * @Route("/documents/{categoryId}/{title}-{page}", name="document_view_category",
     * defaults={"categoryId" = "", "page" = 1, "title"="page"},
     * requirements={"page": "\d+", "title" : "page"})
     */
    public function veiwCategoryAction(Request $request, $categoryId, $page)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params, 
            'CPSEAPIProjectBundle:Document:categoryview.html.twig',
            Document::$pageSize, $page, $categoryId, false, false, true);
    }
    
    /**
     * @Annotations\View()
     * @Route("/api/category/{catId}.{_format}", name="api_listcat",
     * defaults={"catId" = "", "_format" = "json"})
     * @Method("GET")
     */
    public function apiCategoryViewAction(Request $request, $catId)
    {
        $dm = $this->getDocumentManager();
        $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document');
        $qb->field('object_type')->equals(Document::FOLDER);
        $cat = null;
        if (!empty($catId)){
            $cat = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
            $qb->field('parent')->references($cat);
        } else {
            $qb->field('parent')->equals($cat);
        }
        $categories = $qb->getQuery()->Execute();
        
        $results = array();
        if (isset($cat)){
            $results['name'] = $cat->getName();
            $results['id'] = $cat->getId();
            $results['description'] = $cat->getDescription();
            $results['updatedat'] = $cat->getUpdatedAt();
        }
        
        $children = array();
        foreach ($categories as $category) {
            $children[] = $category;
        }
        $results['subCats'] = $children;
        
        return $results;
    }
    /**
     * @Annotations\View()
     * @Route("/api/documents/{catId}.{_format}", name="api_listdocs",
     * defaults={"catId" = "", "_format" = "json"})
     * @Method("GET")
     */
    public function apiDocumentViewAction(Request $request, $catId)
    {
        $dm = $this->getDocumentManager();
        $qb = $dm->createQueryBuilder('CPSEAPIProjectBundle:Document');
        $cat = null;
        if (!empty($catId)){
            $cat = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($catId);
            $qb->field('parent')->references($cat);
        } else {
            $qb->field('parent')->equals($cat);
        }
        $categories = $qb->getQuery()->Execute();
    
        $children = array();
        foreach ($categories as $category) {
            $children[] = $category;
        }
        if (count($children) > 0) {
            if (!isset($cat)){
                $cat = new Document(true);
            }
            $cat->setChildren($children);
        }
    
        return $cat;
    }
    
    /**
     * * @Annotations\View(
     *   template = "CPSEAPIProjectBundle:Document:error.html.twig",
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     * @Route("/api/category", name="api_newcat")
     * @Method("POST")
     */
    public function apiNewFolderAction(Request $request)
    {
        $document = new Document(true);
        $form = $this->createForm(new DocumentType(), $document, array('csrf_protection' => false));
        //For folder, it does not need file and it's parent is optional
        $form->remove('file')
        ->remove('type');
        
        //set to optional
        $form->add('type', 'hidden', array('data' => Document::FOLDER));
        $form->add('parent', 'hidden');
        $form->submit($request);
        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();
            return $this->redirectToRoute('api_listcat', array('catId' => $document->getId()));
            
        }
        $error = $this->get('translator')->trans('Bad request', array(),'document');
        return array(
            'error' => $error
        );
    }
    
    /**
     *
     * @Route("/projects/{projectId}/documents/{title}-{page}", name="document_view_byporject",
     * defaults={"page" = 1, "title"="page"},
     * requirements={"page": "\d+", "title" : "page"})
     */
    public function veiwbyProjectAction(Request $request, $projectId, $page)
    {
        $dm = $this->getDocumentManager();
        $project = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($projectId);
        if (!$project) {
            $traslator = $this->get('translator');
            if (! $document) {
                throw $this->createNotFoundException(
                    $traslator->trans(KeyString::PROJECT_NOTFOUND, array(), 'project') . '' . $projectId);
            }
        }
        
        $catId = null;
        if ($project->getReferences() != null) {
            $catId = $project->getReferences()->getId();
        } else {
            // Render empty results
            return $this->render('CPSEAPIProjectBundle:Document:projectview.html.twig', array(
                'documents' => array(),
                'deleteForms' => array(),
                'total' => 0,
                'currentPage' => 1,
                'pageSize' => Document::$pageSize,
                'currentSort' => isset($params['sortedBy']) ? $params['sortedBy'] : null,
                'currentOrder' => isset($params['order']) ? $params['order'] : null,
                'currentCategory' => $project->getReferences(),
                'project'   => $project
            ));
        }
        $otherParams = array('project' => $project);
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params,
            'CPSEAPIProjectBundle:Document:projectview.html.twig',
            Document::$pageSize, $page, $catId, false, false, true, $otherParams);
    }
    
    /**
     *
     * @Route("/projects/{projectId}/products/{title}-{page}", name="document_view_porjectproduct",
     * defaults={"page" = 1, "title"="page"},
     * requirements={"page": "\d+", "title" : "page"})
     */
    public function veiwbyProductsAction(Request $request, $projectId, $page)
    {
        $dm = $this->getDocumentManager();
        $project = $dm->getRepository('CPSEAPIProjectBundle:Project')->find($projectId);
        if (!$project) {
            $traslator = $this->get('translator');
            if (! $document) {
                throw $this->createNotFoundException(
                    $traslator->trans(KeyString::PROJECT_NOTFOUND, array(), 'project') . '' . $projectId);
            }
        }
    
        $catId = null;
        if ($project->getProducts() != null) {
            $catId = $project->getProducts()->getId();
        } else {
            // Render empty results
            return $this->render('CPSEAPIProjectBundle:Document:projectview.html.twig', array(
                'documents' => array(),
                'deleteForms' => array(),
                'total' => 0,
                'currentPage' => 1,
                'pageSize' => Document::$pageSize,
                'currentSort' => isset($params['sortedBy']) ? $params['sortedBy'] : null,
                'currentOrder' => isset($params['order']) ? $params['order'] : null,
                'currentCategory' => $project->getProducts(),
                'project'   => $project
            ));
        }
        $otherParams = array('project' => $project);
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params,
            'CPSEAPIProjectBundle:Document:projectview.html.twig',
            Document::$pageSize, $page, $catId, false, false, true, $otherParams);
    }
    
    /**
     *
     * @Route("/documenttype/{categoryId}", name="document_type",
     * defaults={"categoryId" = ""},)
     */
    public function veiwDocumentTypeAction(Request $request, $categoryId)
    {
        $params = array();
        $params = array_merge($request->request->all(), $request->query->all());
        return $this->showAllDocuments($params,
            'CPSEAPIProjectBundle:Document:categoryview.html.twig',
            null, 1, $categoryId, false, true, true);
    }
    
    /**
     * Finds and displays a Document document.
     *
     * @Route("/document/{id}", name="document_show")
     *
     * @param string $id
     *            The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();
    
        $document = $dm->getRepository('CPSEAPIProjectBundle:Document')->find($id);
    
        if (! $document) {
            throw $this->createNotFoundException('Unable to find Document document.');
        }
    
        $deleteForm = $this->createDeleteForm($id);
    
        return $this->render('CPSEAPIProjectBundle:Document:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView()
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array(
            'id' => $id
        ))
            ->add('id', 'hidden')
            ->getForm();
    }

    private function createSortForm()
    {
        return $this->createFormBuilder()
            ->add('sortedBy', 'hidden')
            ->add('Order', 'hidden')
            ->add('currentPage', 'hidden')
            ->getForm();
    }

    private function createSearchForm($query)
    {
        return $this->createFormBuilder($query, array(
            'method' => 'GET'
        ))
            ->add('sortedBy', 'hidden', array(
            'required' => false
        ))
            ->add('order', 'hidden', array(
            'required' => false
        ))
            ->add('currentPage', 'hidden', array(
            'required' => false
        ))
            ->add('year', 'number', array(
            'required' => false
        ))
            ->add('category', 'text', array(
            'required' => false
        ))
            ->add('q', 'text', array(
            'required' => false
        ))
            ->add('project', 'document', array(
            'class' => 'CPSEAPIProjectBundle:Project',
            'choice_label' => 'name',
            'required' => false
        ))
            ->getForm();
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
