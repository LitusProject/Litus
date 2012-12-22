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
            ->findAllUncompleted(10);

        $subjectComments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findLast(10);

        $activeSessions = array();
        if ($this->getAuthentication()->isAuthenticated()) {
            $activeSessions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\Session')
                ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }

        $currentSession = $this->getAuthentication()->getSessionObject();

        return new ViewModel(
            array(
                'profActions' => $profActions,
                'subjectComments' => $subjectComments,
                'activeSessions' => $activeSessions,
                'currentSession' => $currentSession,
                'versions' => array(
                    'php' => phpversion(),
                    'zf' => \Zend\Version\Version::VERSION,
                    'doctrine' => \Doctrine\Common\Version::VERSION
                ),
            )
        );
    }
}
