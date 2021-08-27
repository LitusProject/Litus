<?php

namespace CalendarBundle\Controller\Admin;

use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        return new ViewModel();
    }

    public function exportAction()
    {
        $file = 'data/calendar/registration/export.html';
        $handle = fopen($file, 'r');
        $data = fread($handle, filesize($file));
        fclose($handle);

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllMembers($this->getCurrentAcademicYear());

        $membersJson = array();
        foreach ($members as $member) {
            $membersJson[] = array(
                'id'             => $member->getId(),
                'identification' => $member->getUniversityIdentification(),
            );
        }

        $data = str_replace('{{ members }}', json_encode($membersJson), $data);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="registrations.html"',
                'Content-Type'        => 'text/html',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }
}
