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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Version\Version;
use DateInterval;
use DateTime;
use PackageVersions\Versions;
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
        $enableRegistration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.enable_registration');

        $registrationsGraph = null;
        if ($enableRegistration) {
            $registrationsGraph = $this->getRegistrationsGraph();
        }

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

        $versions = $this->getVersions();

        return new ViewModel(
            array(
                'profActions'        => $profActions,
                'subjectComments'    => $subjectComments,
                'subjectReplies'     => $subjectReplies,
                'activeSessions'     => $activeSessions,
                'currentSession'     => $currentSession,
                'registrationsGraph' => $registrationsGraph,
                'versions'           => $versions,
            )
        );
    }

    /**
     * @return array
     */
    private function getVersions()
    {
        preg_match('/(\d.\d.\d)/', phpversion(), $phpVersion);
        preg_match('/(\d.\d.\d)/', Versions::getVersion('zendframework/zend-mvc'), $zfVersion);

        return array(
            'php'   => $phpVersion[0],
            'zf'    => $zfVersion[0],
            'litus' => Version::getShortCommitHash(),
        );
    }

    /**
     * @param  Analytics $analytics
     * @return array
     */
    private function getVisitsGraph(Analytics $analytics)
    {
        if ($this->getCache() !== null) {
            if ($this->getCache()->hasItem('CommonBundle_Controller_IndexController_VisitsGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph')['expirationTime'] > $now) {
                    return $this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph');
                }
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_IndexController_VisitsGraph',
                $this->getVisitsGraphData($analytics)
            );

            return $this->getCache()->getItem('CommonBundle_Controller_IndexController_VisitsGraph');
        }

        return $this->getVisitsGraphData($analytics);
    }

    /**
     * @param  Analytics $analytics
     * @return array
     */
    private function getVisitsGraphData(Analytics $analytics)
    {
        $now = new DateTime();

        $visitsGraphData = array(
            'expirationTime' => $now->add(new DateInterval('P1D')),

            'labels'  => array(),
            'dataset' => array(),
        );

        foreach ((array) $analytics->getUniqueVisitors('previous7') as $dateString => $count) {
            $date = new DateTime($dateString);

            $visitsGraphData['labels'][] = $date->format('d/m/Y');
            $visitsGraphData['dataset'][] = $count;
        }

        return $visitsGraphData;
    }

    /**
     * @return array
     */
    private function getRegistrationsGraph()
    {
        if ($this->getCache() !== null) {
            if ($this->getCache()->hasItem('CommonBundle_Controller_IndexController_RegistrationsGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationsGraph')['expirationTime'] > $now) {
                    return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationsGraph');
                }
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_IndexController_RegistrationsGraph',
                $this->getRegistrationsGraphData()
            );

            return $this->getCache()->getItem('CommonBundle_Controller_IndexController_RegistrationsGraph');
        }

        return $this->getRegistrationsGraphData();
    }

    /**
     * @return array
     */
    private function getRegistrationsGraphData()
    {
        $now = new DateTime();

        $registationGraphData = array(
            'expirationTime' => $now->add(new DateInterval('PT1H')),

            'labels'  => array(),
            'dataset' => array(),
        );

        $data = array();

        for ($i = 0; $i < 7; $i++) {
            $today = new DateTime('midnight');
            $labelDate = $today->sub(new DateInterval('P' . $i . 'D'));
            $data[$labelDate->format('d/m/Y')] = 0;
        }

        $today = new DateTime('midnight');
        $registrations = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findAllSince($today->sub(new DateInterval('P6D')));

        $data = array();
        foreach ($registrations as $registration) {
            if (isset($data[$registration->getTimestamp()->format('d/m/Y')])) {
                $data[$registration->getTimestamp()->format('d/m/Y')]++;
            }
        }

        foreach (array_reverse($data) as $label => $value) {
            $registationGraphData['labels'][] = $label;
            $registationGraphData['dataset'][] = $value;
        }

        return $registationGraphData;
    }
}
