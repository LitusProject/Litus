<?php

namespace Admin;

use \Admin\Form\Config\Add as AddForm;
use \Admin\Form\Config\Edit as EditForm;

use \Litus\Controller\Exception\HasNoAccessException;

use \Litus\Entity\Config\Config;


class ConfigController extends \Litus\Controller\Action
{

    public function addAction()
    {
        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $key = $formData['prefix'] . Config::SEPARATOR . $formData['name'];

                $config = new Config($key);
                $config->setDescription($formData['description'])
                    ->setValue($formData['value']);

                $this->getEntityManager()->persist($config);

                $this->view->configCreated = true;
                $form = new AddForm();
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        /**
         * @var \Litus\Entity\Config\Config $config
         */
        $config = $this->getEntityManager()
                    ->getRepository('Litus\Entity\Config\Config')
                    ->find($this->getRequest()->getParam('id'));

        $prefix = $config->getKey();
        $prefix = preg_split('\\' . Config::SEPARATOR, $prefix);
        $prefix = $prefix[0];

        if(!$this->broker('hasAccess')->__invoke('admin','config','edit.' . $prefix))
            throw new HasNoAccessException('You don\'t have the right privileges to edit configurations '
                    . 'starting with ' . $prefix . '.');

        $form = new EditForm($config);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if($form->isValid($formData)) {
                $config->setValue($formData['value'])
                    ->setDescription($formData['description']);

                $this->view->configEdited = true;
            }
        }
    }
}
