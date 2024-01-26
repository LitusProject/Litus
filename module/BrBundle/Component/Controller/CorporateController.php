<?php

namespace BrBundle\Component\Controller;

use BrBundle\Entity\User\Person\Corporate;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use CommonBundle\Component\Util\AcademicYear;
use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CorporateController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  \Laminas\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->cvArchiveYears = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_archive_years')
        );

        $result->loginForm = $this->getForm('common_auth_login')
            ->setAttribute('class', '')
            ->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'br_corporate_auth',
                    array(
                        'action' => 'login',
                    )
                )
            );
        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');

        $e->setResult($result);

        return $result;
    }

    /**
     * @param boolean $login
     * @return Corporate|null
     */
    protected function getCorporateEntity(bool $login = true)
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();

            if ($person instanceof Corporate) {
                return $person;
            }
        }

        if (!$login) {
            throw new HasNoAccessException('You do not have sufficient permissions to access this resource');
        }

        $this->redirect()->toRoute(
            'br_corporate_index',
            array(
                'action' => 'login',
            )
        );
        return null;
    }

    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }

        return AcademicYear::getUniversityYear($this->getEntityManager(), $date);
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'br_corporate_index',
            'redirect_route' => 'br_corporate_index',
        );
    }
}
