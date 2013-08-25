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

namespace ApiBundle\Component\Controller\ActionController;

use ApiBundle\Component\Controller\Request\Exception\NoPostRequestException,
    ApiBundle\Component\Controller\Request\Exception\NoKeyException,
    ApiBundle\Component\Controller\Request\Exception\InvalidKeyException,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ApiController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        if ('development' != getenv('APPLICATION_ENV')) {
            if (!$this->getRequest()->isPost()) {
                throw new NoPostRequestException(
                    'The API should be accessed using POST requests'
                );
            }

            if ('' == $this->getRequest()->getPost('key', '')) {
                throw new NoKeyException(
                    'No API key was provided with the request'
                );
            }

            $key = $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Key')
                ->findOneActiveByCode($this->getRequest()->getPost('key'));

            if (!$key->validate($_SERVER['REMOTE_ADDR'])) {
                throw new InvalidKeyException(
                    'The given API key was invalid'
                );
            }
        }

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return null;
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if (!$organization)
            return parent::getCurrentAcademicYear();

        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $now = new DateTime();
        $start = new DateTime(
            str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.prof_start_academic_year')
            )
        );
        $start->add(new DateInterval('P1Y'));

        if ($now > $start) {
            $startAcademicYear->add(new DateInterval('P1Y2M'));
            $startAcademicYear = AcademicYear::getStartOfAcademicYear($startAcademicYear);
        }

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        return $academicYear;
    }
}
