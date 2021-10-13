<?php

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\Util\AcademicYear;
use CudiBundle\Entity\Stock\Period;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getAcademicYearEntity()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }

        return AcademicYear::getOrganizationYear($this->getEntityManager(), $date);
    }

    /**
     * Returns the active stock period.
     *
     * @return Period|null
     */
    protected function getActiveStockPeriodEntity()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        if (!($period instanceof Period)) {
            $this->flashMessenger()->error(
                'Error',
                'There is no active stock period!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_period',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $period->setEntityManager($this->getEntityManager());

        return $period;
    }
}
