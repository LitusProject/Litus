<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sale\Article\Discount;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sale\Article\Discount\Template,
    CudiBundle\Form\Admin\Sales\Article\Discounts\Template\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Article\Discounts\Template\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * TemplateController
 * @author Dario Incalza <dario.incalza@litus.cc>
 */
class TemplateController extends \CudiBundle\Component\Controller\ActionController
{

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['organization'] != '0') {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['organization']);
                } else {
                    $organization = null;
                }

				$template = new Template(
					$formData['name'],
					$formData['value'],
					$formData['method'],
					$formData['type'],
					$formData['rounding'],
					$formData['apply_once'],
					$organization
				);

                $this->getEntityManager()->persist($template);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The template was successfully created!'
                    )
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

	public function manageAction()
    {
        $templates = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $templates,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'templates' => $templates,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($template = $this->_getTemplate()))
            return new ViewModel();

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

        if (!($template = $this->_getTemplate()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $template);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['organization'] != '0') {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['organization']);
                } else {
                    $organization = null;
                }

                $template->setName($formData['name'])
                    ->setValue($formData['value'])
                    ->setMethod($formData['method'])
                    ->setType($formData['type'])
                    ->setRounding($formData['rounding'])
                    ->setApplyOnce($formData['apply_once'])
                    ->setOrganization($organization);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The discount template was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_discount_template',
                    array(
                        'action' => 'manage'
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

    private function _getTemplate()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the template!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article_discount_template',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $template = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findOneById($this->getParam('id'));

        if (null === $template) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No template with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article_discount_template',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $template;
    }
}
