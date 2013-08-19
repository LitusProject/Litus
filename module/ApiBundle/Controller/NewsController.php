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

namespace ApiBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * NewsController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class NewsController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getLastAction()
    {
        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }
}
