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

use BrBundle\Entity\Contract,
    BrBundle\Entity\Contract\ContractEntry,
    BrBundle\Entity\Product\Order,
    BrBundle\Entity\Contract\Section,
    BrBundle\Entity\Contract\Composition,
    BrBundle\Entity\Product\OrderEntry,
    BrBundle\Form\Admin\Order\Add as AddForm,
    BrBundle\Form\Admin\Order\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * OverviewController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OverviewController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product\Order',
            $this->getParam('page')
        );

        $array = $this->_getOverview();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    private function _getOverview(){
        //TODO

        // $authorsID = $this->getEntityManager()
        //     ->getRepository('BrBundle\Entity\Contract')
        //     ->findContractAuthors();
        // $array = array();
        // foreach ($authorsID as $author) {
        //     print_r($author[1]);
        //     $person = $this->getEntityManager()
        //         ->getRepository('BrBundle\Entity\Contract')
        //         ->findAuthorByID($author);
        //     array_push($array,$person);
        // }
        // print_r($array);
        // return $array;
        $array = array();
        $person = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findAuthorByID(1);
        $array[] = $person;
        return $array;
    }




}
