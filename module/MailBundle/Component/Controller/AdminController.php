<?php

namespace MailBundle\Component\Controller;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AdminController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function findCurrentAcademicYear()
    {
        return $this->getCurrentAcademicYear(false);
    }
}
