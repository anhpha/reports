<?php

namespace CPSE\API\ProjectBundle\Classes;

class KeyString {
    //TASK
    const TASK_CREATED = 'task_created';
    const TASK_ASSIGNED = 'task_assigned';
    const TASK_PENDING = 'task_pending';
    const TASK_FINISHED = 'task_finished';
    //STATUS
    const STATUS_CHANGE_PREFIX = 'Change status from';
    const STATUS_CHANGE_TO = 'to';
    //DOCUMENT
    const DOCUMENT_NOTFOUND= 'Unable to find this document';
    const DOCUMENT_BEINGUSED= 'This document is using by an other';
    
    //PROJECT CATEGORY
    const PROJECTCATEGORY_CHILDRENEXISTED = 'This have child project!';
    const PROJECT_NOTFOUND= 'Unable to find project';
    
    //PROJECT
    const PROJECT_FOLDER_REFERENCE = "Documents";
    const PROJECT_FOLDER_PRODUCT = "Reports";
}
