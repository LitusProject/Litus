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

namespace CudiBundle\Controller\Admin\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article\Discount\Template,
    Zend\View\Model\ViewModel;

/**
 * TemplateController
 * @author Dario Incalza <dario.incalza@litus.cc>
 */
class TemplateController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sale\Article\Discount\Template',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'DESC',
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
        $form = $this->getForm('cudi_sale_article_discount_template_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The template was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_discount_template',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        $templates = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();

        return new ViewModel(
            array(
                'templates' => $templates,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($template = $this->getTemplate())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($template);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function editAction()
    {
        if (!($template = $this->getTemplate())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_discount_template_edit', $template);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The discount template was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_discount_template',
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

    /**
     * @return Template
     */
    private function getTemplate()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the template!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article_discount_template',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $template = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findOneById($this->getParam('id'));

        if (null === $template) {
            $this->flashMessenger()->error(
                'Error',
                'No template with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article_discount_template',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $template;
    }
}
