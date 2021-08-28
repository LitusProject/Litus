<?php

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Node\FAQ\FAQ;
use CommonBundle\Entity\General\Node\FAQ\FAQPageMap;
use Laminas\View\Model\ViewModel;

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
        $pageForm = $this->getForm('common_FAQ_page');

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
            )
        );
    }

    public function editAction()
    {
        $faq = $this->getFAQEntity();
        if ($faq === null) {
            return new ViewModel();
        }

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByFAQQuery($faq)->getResult();

        $faqForm = $this->getForm('common_FAQ_edit', array('faq' => $faq));
        $pageForm = $this->getForm('common_FAQ_page', array('faq' => $faq));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $faqForm->setData($formData);
            $pageForm->setData($formData);

            if (in_array('page_add', $formData) && $pageForm->isValid() && !in_array('submit', $formData)) {
                $page = $this->getEntityManager()->getRepository('PageBundle\Entity\Node\Page')
                    ->findOneById(intval($formData['page_typeahead']['id']));

                $map = new FAQPageMap($faq, $page);
                $this->getEntityManager()->persist($map);
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
            } elseif (in_array('submit', $formData)  && $faqForm->isValid() && !in_array('page_add', $formData)) {
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
                'maps' => $maps,
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

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByFAQQuery($faq)->getResult();

        foreach ($maps as $map) {
            $this->getEntityManager()->remove($map);
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

        $map = $this->getMapEntity();
        if ($map === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($map);
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
     * @return FAQPageMap|null
     */
    private function getMapEntity()
    {
        $map = $this->getEntityById('CommonBundle\Entity\General\Node\FAQ\FAQPageMap', 'map');
        if (!($map instanceof FAQPageMap) || !$map->getPage()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No FAQ-page-map was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_faq',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $map;
    }
}
