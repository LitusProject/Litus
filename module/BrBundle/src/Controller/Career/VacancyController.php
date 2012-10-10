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
 * VacancyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        return new ViewModel();
    }

    public function viewAction()
    {
        return new ViewModel();
    }
}
