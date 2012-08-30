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

namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Language,
    CommonBundle\Form\Auth\Login as LoginForm,
    DateInterval,
    DateTime,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AdminController extends \CommonBundle\Component\Controller\ActionController
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

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        if (null === $language) {
            $language = new Language(
                'en', 'English'
            );
        }

        $result->language = $language;
        $result->now = array(
            'iso8601' => date('c', time()),
            'display' => date('l, F j Y, H:i', time())
        );

        $e->setResult($result);

        return $result;
    }

    /**
     * Initializes the localization
     *
     * @return void
     */
    protected function initLocalization()
    {
        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        if (null === $language) {
            $language = new Language(
                'en', 'English'
            );
            $this->getEntityManager->persist($language);
            $this->getEntityManager->flush();
        }

        $this->getTranslator()->setCache($this->getCache());
        $this->getTranslator()->setLocale($language->getAbbrev());

        \Zend\Validator\AbstractValidator::setDefaultTranslator($this->getTranslator());

        if ($this->getAuthentication()->isAuthenticated()) {
            $this->getAuthentication()->getPersonObject()->setLanguage($language);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $start = new DateTime(
            str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            )
        );

        $next = clone $start;
        $next->add(new DateInterval('P1Y'));
        if ($next <= new DateTime())
            $start = $next;

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

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
