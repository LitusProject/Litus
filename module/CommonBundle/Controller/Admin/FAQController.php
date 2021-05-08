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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Node\FAQ\FAQ;
use Laminas\Validator\ValidatorChain;
use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Node\Page;

/**
 * FAQController
 *
 * @author Robin Wroblowski (rip-off from PageController)
 */
class FAQController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $faqs = array();
        if ($this->getParam('field') !== null) {
            $faqs = $this->search();
        }

        if (count($faqs) == 0) {
            $faqs = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQ')
                ->findAll();
        }

        foreach ($faqs as $key => $faq) {
            if (!$faq->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($faqs[$key]);
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $faqs,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('common_FAQ_add');
        $pageForm = $this->getForm('common_FAQ_page', array());

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $faq = $form->hydrateObject();

                $this->getEntityManager()->persist($faq);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The FAQ was successfully added!'
                );

                $this->redirect()->toRoute(
                    'common_admin_faq',
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
                'pageForm' => $pageForm,
                'pages' => array(),
            )
        );
    }

    public function editAction()
    {
        $faq = $this->getFAQEntity();
        if ($faq === null) {
            return new ViewModel();
        }
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findAllByFAQQuery($faq)->getResult();

        $faqForm = $this->getForm('common_FAQ_edit', array('faq' => $faq));
        $pageForm = $this->getForm('common_FAQ_page', array('faq' => $faq));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $faqForm->setData($formData);
            $pageForm->setData($formData);

//            if (isset($formData['page_add']) && $pageForm->isValid()) {
            if (isset($formData['page_add'])) {
                error_log("hereeeeeeeeeeeee!");

                $page = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Node\Page')
                    ->findOneById(intval($formData['page_typeahead']['id']));

                $page->addFAQ($faq);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The page was successfully added!'
                );

                $this->redirect()->toRoute(
                    'common_admin_faq',
                    array(
                        'action' => 'edit',
                        'id'   => $faq->getId(),
                    )
                );
                return new ViewModel();
            }

            elseif (isset($formData['faq_edit']) && $faqForm->isValid()) {
                error_log("here2!");
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The faq was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'common_admin_faq',
                    array(
                        'action' => 'manage',
                    )
                );
                return new ViewModel();
            }

        }

        return new ViewModel(
            array(
                'form' => $faqForm,
                'pageForm' => $pageForm,
                'pages' => $pages,
                'faq' => $faq,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $faq = $this->getFAQEntity();
        if ($faq === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($faq);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function deletePageMapAction()
    {
        $this->initAjax();

        $faq = $this->getFAQEntity();
        if ($faq === null) {
            return new ViewModel();
        }
        $page = $this->getPageEntity();
        if ($page === null) {
            return new ViewModel();
        }
        $page->removeFAQ($faq);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $pages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQ')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults(15)
            ->getResult();

        $result = array();
        foreach ($pages as $page) {
            $item = (object) array();
            $item->id = $page->getId();
            $item->title = $page->getName();
            $item->value = $page->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $faqs = $this->search();

        foreach ($faqs as $key => $faq) {
            if (!$faq->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($faqs[$key]);
            }
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($faqs, $numResults);

        $result = array();
        foreach ($faqs as $faq) {
            $item = (object) array();
            $item->id = $faq->getId();
            $item->name = $faq->getTitle($this->getLanguage());
            $item->author = $faq->getCreationPerson()->getFullName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return array
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQ')
                    ->findAllByName($this->getParam('string'));
        }

        return array();
    }

    /**
     * @return FAQ|null
     */
    private function getFAQEntity()
    {
        $faq = $this->getEntityById('CommonBundle\Entity\General\Node\FAQ\FAQ');
        if (!($faq instanceof FAQ) || !$faq->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No faq was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_faq',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $faq;
    }

    /**
     * @return Page|null
     */
    private function getPageEntity()
    {
        $page = $this->getEntityById('PageBundle\Entity\Node\Page', $this->getParam('map'));
        if (!($page instanceof Page) || !$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No page was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $page;
    }
}
