<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Entity\Section;

class SectionController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Section',
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
        $form = $this->getForm('mail_section_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $section = $form->hydrateObject();

                $this->getEntityManager()->persist($section);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The section was succesfully added!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_section',
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

        $section = $this->getSectionEntity();
        if ($section === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($section);
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

        $sections = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($sections as $section) {
            $item = (object) array();
            $item->id = $section->getId();
            $item->section = $section->getName();
            $item->attribute = $section->getAttribute();
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
            case 'section':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\Section')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Section|null
     */
    private function getSectionEntity()
    {
        $section = $this->getEntityById('MailBundle\Entity\Section');

        if (!($section instanceof Section)) {
            $this->flashMessenger()->error(
                'Error',
                'No section was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_section',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $section;
    }
}