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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use MailBundle\Entity\Alias;
use Zend\View\Model\ViewModel;

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

        if (!($alias = $this->getAliasEntity())) {
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
