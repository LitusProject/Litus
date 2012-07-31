<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $profActions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllUncompleted();
        array_splice($profActions, 10);
        
        return new ViewModel(
            array(
                'profActions' => $profActions,
                'versions' => array(
                    'php' => phpversion(),
                    'zf' => \Zend\Version::VERSION,
                    'doctrine' => \Doctrine\Common\Version::VERSION
                ),
            )
        );
    }
}
