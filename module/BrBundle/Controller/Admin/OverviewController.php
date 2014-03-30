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

namespace BrBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

/**
 * OverviewController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OverviewController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $array = $this->_getOverview();

        return new ViewModel(
            array(
                'array' => $array,
            )
        );
    }

    public function viewAction()
    {
        $person = $this->_getAuthor();

        $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findContractsByAuthorID($person);

        return new ViewModel(
            array(
                'contracts' => $contracts,
            )
        );
    }

    private function _getOverview(){

        //TODO extremely dirty solution -> can be put in one single query normally!
        //TODO has to be cleaned up..

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractAuthors();
        $collection = array();
        foreach ($ids as $id => $val) {
            $result = array();

            $person = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAuthorByID($val[1]);

            //TODO this query returns a array instead of a single element, has to be fixed.  So loop can be avoided.

            foreach ($person as $pers) {
                $result['person'] = $pers;
                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractAmountByPerson($pers);
                $result['amount'] = $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractedRevenueByPerson($pers);
                $result['contract'] = $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getPaidRevenueByPerson($pers);
                $result['paid'] = $amount;

                array_push($collection, $result);
            }
        }
        return $collection;


    }

    private function _getAuthor()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the author!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $array = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAuthorByID($this->getParam('id'));
        foreach ($array as $pers) {
            $person = $pers;
        }

        if (null === $person) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No person with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $person;
    }
}
