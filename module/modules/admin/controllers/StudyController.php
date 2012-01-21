<?php

namespace Admin;

use \Admin\Form\Study\Add as AddForm;

use \Doctrine\ORM\QueryBuilder;

use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Registry;

class StudyController extends \Litus\Controller\Action
{
    public function init()
    {

    }

    public function indexAction()
    {
        $this->_forward('add');
    }

    public function addAction()
    {
        $this->view->form = new AddForm(array());
    }

    public function manageAction()
    {
        $query = new QueryBuilder(Registry::get('EntityManager'));
        $query->select('r')
                ->from('Litus\Entity\Syllabus\Study', 'r');

        $paginator = new Paginator(new ArrayAdapter($query->getQuery()->useResultCache(true)->getResult()));
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }
}
