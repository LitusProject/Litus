<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Entity\Alias;

class AliasController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Alias',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('mail_alias_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $alias = $form->hydrateObject();

                $this->getEntityManager()->persist($alias);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The alias was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_alias',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $alias = $this->getAliasEntity();
        if ($alias === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($alias);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $aliases = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($aliases as $alias) {
            $item = (object) array();
            $item->id = $alias->getId();
            $item->alias = $alias->getName();
            $item->email = $alias->getEmailAddress();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'alias':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\Alias')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Alias|null
     */
    private function getAliasEntity()
    {
        $alias = $this->getEntityById('MailBundle\Entity\Alias');

        if (!($alias instanceof Alias)) {
            $this->flashMessenger()->error(
                'Error',
                'No alias was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_alias',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $alias;
    }
}
