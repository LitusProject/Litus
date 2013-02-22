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

namespace CudiBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * HelpController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HelpController extends \CudiBundle\Component\Controller\ProfController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
