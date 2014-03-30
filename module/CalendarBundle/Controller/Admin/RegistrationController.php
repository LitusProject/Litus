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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

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
            $barcode = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Barcode')
                ->findOneByPerson($member);

            $membersJson[] = array(
                'id' => $member->getId(),
                'name' => $member->getFullName(),
                'firstname' => $member->getFirstName(),
                'lastname' => $member->getLastName(),
                'identification' => $member->getUniversityIdentification(),
                'barcode' => isset($barcode) ? $barcode->getBarcode() : '',
            );
        }

        $data = str_replace('{{ members }}', json_encode($membersJson), $data);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="registrations.html"',
            'Content-Type'        => 'text/html',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }
}
