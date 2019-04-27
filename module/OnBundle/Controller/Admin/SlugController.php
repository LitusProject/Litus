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

namespace OnBundle\Controller\Admin;

use OnBundle\Entity\Slug;
use Zend\View\Model\ViewModel;

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
