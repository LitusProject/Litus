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

/**
 * Mainly serves the admin dashboard.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class DashboardController extends \CommonBundle\Component\Controller\ActionController
{
    public function indexAction()
    {    
        return array(
        	'server_environment' => $this->getRequest()->server()->toArray(),
        	'versions' => array(
        		'php' => phpversion(),
        		'zf' => \Zend\Version::VERSION,
        		'doctrine' => \Doctrine\Common\Version::VERSION
        	)
        );
    }
}