<?php

namespace CudiBundle\Controller\Admin\Sale\Article\Discount;

use CudiBundle\Entity\Sale\Article\Discount\Template;
use Laminas\View\Model\ViewModel;

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
                'paginator'         => $paginator,
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
                'form'      => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $template = $this->getTemplateEntity();
        if ($template === null) {
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
        $template = $this->getTemplateEntity();
        if ($template === null) {
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
     * @return Template|null
     */
    private function getTemplateEntity()
    {
        $template = $this->getEntityById('CudiBundle\Entity\Sale\Article\Discount\Template');

        if (!($template instanceof Template)) {
            $this->flashMessenger()->error(
                'Error',
                'No template was found!'
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
