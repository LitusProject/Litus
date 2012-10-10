<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Controller\Career;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * CompanyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CompanyController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        return new ViewModel();
    }

    public function viewAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        return new ViewModel(
            array(
                'company' => $company
            )
        );
    }

    private function _getCompany()
    {
        if (null === $this->getParam('company')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No name was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'career_company',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneBySlug($this->getParam('company'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given name was found!'
                )
            );

            $this->redirect()->toRoute(
                'career_company',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $company;
    }
}
