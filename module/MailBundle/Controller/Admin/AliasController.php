<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use MailBundle\Entity\Alias\Academic as Alias,
    MailBundle\Form\Admin\Alias\Add as AddForm,
    Zend\View\Model\ViewModel;

class AliasController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Alias',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']);
                }

                $alias = new Alias(
                    $formData['alias'], $academic
                );
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

        if (!($alias = $this->_getAlias()))
            return new ViewModel();

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

        $aliases = $this->_search()
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
    private function _search()
    {
        switch ($this->getParam('field')) {
            case 'alias':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\Alias')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    private function _getAlias()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the alias!'
            );

            $this->redirect()->toRoute(
                'mail_admin_alias',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $alias = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
            ->findOneById($this->getParam('id'));

        if (null === $alias) {
            $this->flashMessenger()->error(
                'Error',
                'No alias with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_alias',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $alias;
    }
}
