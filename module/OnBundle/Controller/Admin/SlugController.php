<?php

namespace OnBundle\Controller\Admin;

use DateTime;
use Laminas\View\Model\ViewModel;
use OnBundle\Entity\Slug;

/**
 * SlugController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SlugController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'OnBundle\Entity\Slug',
            $this->getParam('page'),
            array(
                'active' => true,
            ),
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

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'OnBundle\Entity\Slug',
            $this->getParam('page'),
            array(
                'active' => false,
            ),
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
        $form = $this->getForm('on_slug_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $slug = $form->hydrateObject();

                $this->getEntityManager()->persist($slug);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The slug was successfully created!'
                );

                $this->redirect()->toRoute(
                    'on_admin_slug',
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

    public function editAction()
    {
        $slug = $this->getSlugEntity();
        if ($slug === null) {
            return new ViewModel();
        }

        $form = $this->getForm('on_slug_edit', array('slug' => $slug));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The slug was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'on_admin_slug',
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
                'slug' => $slug,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $slug = $this->getSlugEntity();
        if ($slug === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($slug);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function cleanAction()
    {
        $slugs = $this->getEntityManager()
            ->getRepository('OnBundle\Entity\Slug')
            ->findAllActive();

        $now = new DateTime();
        foreach ($slugs as $slug) {
            if ($slug->getExpirationDate() < $now && $slug->getExpirationDate() !== null) {
                $slug->setActive(false);
            }
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'on_admin_slug',
            array(
                'action' => 'manage',
            )
        );
    }

    public function clearOldAction()
    {
        $slugs = $this->getEntityManager()
            ->getRepository('OnBundle\Entity\Slug')
            ->findAllOld();

        $now = new DateTime();
        foreach ($slugs as $slug) {
            if ($slug->getExpirationDate() < $now && $slug->getExpirationDate() !== null) {
                $this->getEntityManager()->remove($slug);
            }
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'on_admin_slug',
            array(
                'action' => 'old',
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $slugs = $this->search();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($slugs, $numResults);

        $result = array();
        foreach ($slugs as $slug) {
            $item = (object) array();
            $item->id = $slug->getId();
            $item->name = $slug->getName();
            $item->url = $slug->getUrl();
            $item->hits = $slug->getHits();
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
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('OnBundle\Entity\Slug')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Slug|null
     */
    private function getSlugEntity()
    {
        $slug = $this->getEntityManager()
            ->getRepository('OnBundle\Entity\Slug')
            ->findOneById($this->getParam('id', 0));

        if (!($slug instanceof Slug)) {
            $this->flashMessenger()->error(
                'Error',
                'No slug was found!'
            );

            $this->redirect()->toRoute(
                'on_admin_slug',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $slug;
    }
}
