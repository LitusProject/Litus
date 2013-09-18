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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Piwik\Analytics,
    DateInterval,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class IndexController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $piwikEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.piwik_enabled');

        $piwik = null;
        if ('development' != getenv('APPLICATION_ENV') && $piwikEnabled) {
            $analytics = new Analytics(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_api_url'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_token_auth'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.piwik_id_site')
            );

            $piwik = array(
                'uniqueVisitors' => $analytics->getUniqueVisitors(),
                'liveCounters' => $analytics->getLiveCounters(),
                'visitsSummary' => $analytics->getVisitsSummary()
            );
        }

        $registrationEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.registration_enabled');

        $registrationGraph = null;
        if ($registrationEnabled)
            $registrationGraph = $this->_getRegistrationGraph();

        $profActions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllUncompleted(10);

        $subjectComments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findLast(10);

        $subjectReplies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Reply')
            ->findLast(10);

        $activeSessions = array();
        if ($this->getAuthentication()->isAuthenticated()) {
            $activeSessions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }

        $currentSession = $this->getAuthentication()->getSessionObject();

        return new ViewModel(
            array(
                'profActions' => $profActions,
                'subjectComments' => $subjectComments,
                'subjectReplies' => $subjectReplies,
                'activeSessions' => $activeSessions,
                'currentSession' => $currentSession,
                'piwik' => $piwik,
                'registrationGraph' => $registrationGraph,
                'versions' => array(
                    'php' => phpversion(),
                    'zf' => \Zend\Version\Version::VERSION,
                    'doctrine' => \Doctrine\Common\Version::VERSION
                ),
            )
        );
    }

    private function _getRegistrationGraph()
    {
        if (null !== $this->getCache()) {
            if($this->getCache()->hasItem('CommonBundle_Controller_IndexController_RegistrationGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph')['expirationTime'] > $now)
                    return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph');
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_IndexController_RegistrationGraph',
                $this->_getRegistrationGraphData()
            );

            return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationGraph');
        }

        return $this->_getRegistrationGraphData();
    }

    private function _getRegistrationGraphData()
    {
        $now = new DateTime();

        $registationGraphData = array(
            'expirationTime' => $now->add(new DateInterval('PT1H')),

            'labels' => array(),
            'dataset' => array()
        );

        $registrations = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findByAcademicYear($this->getCurrentAcademicYear());

        $data = array();
        foreach ($registrations as $registration) {
            isset($data[$registration->getTimestamp()->format('d/m/Y')])
                ? $data[$registration->getTimestamp()->format('d/m/Y')]++
                : $data[$registration->getTimestamp()->format('d/m/Y')] = 1;
        }

        foreach($data as $label => $value) {
            $registationGraphData['labels'][] = $label;
            $registationGraphData['dataset'][] = $value;
        }

        return $registationGraphData;
    }
}
