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

namespace ApiBundle\Controller;

use ShiftBundle\Entity\Shift\Responsible,
    ShiftBundle\Entity\Shift\Volunteer,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function activeAction()
    {
        $this->initJson();

        if (null === $this->getAccessToken() || null === $this->_getPerson())
            return $this->error(401, 'The access token is not valid');

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActive();

        $signedUp = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($this->_getPerson());

        $result = array();
        foreach ($shifts as $shift) {
            $result[] = array(
                'id'                    => $shift->getId(),

                'canHaveAsResponsible'  => $shift->canHaveAsResponsible($this->getEntityManager(), $this->_getPerson()),
                'canHaveAsVolunteer'    => $shift->canHaveAsVolunteer($this->getEntityManager(), $this->_getPerson()),
                'description'           => $shift->getDescription(),
                'currentNbResponsibles' => count($shift->getResponsibles()),
                'currentNbVolunteers'   => count($shift->getVolunteers()),
                'endDate'               => $shift->getEndDate()->format('c'),
                'signedUp'              => in_array($shift, $signedUp),
                'manager'               => $shift->getManager()->getFullName(),
                'name'                  => $shift->getName(),
                'nbResponsibles'        => $shift->getNbResponsibles(),
                'nbVolunteers'          => $shift->getNbVolunteers(),
                'startDate'             => $shift->getStartDate()->format('c'),

                'location'              => array(
                    'id'        => $shift->getLocation()->getId(),
                    'latitude'  => $shift->getLocation()->getLatitude(),
                    'longitude' => $shift->getLocation()->getLongitude(),
                    'name'      => $shift->getLocation()->getName(),
                ),
                'unit'                  => array(
                    'id'   => $shift->getUnit()->getId(),
                    'name' => $shift->getUnit()->getName(),
                ),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function responsibleAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getAccessToken() || null === $this->_getPerson())
            return $this->error(401, 'The access token is not valid');

        if (null === $this->_getShift())
            return $this->error(500, 'The shift was not found');

        if (!($this->_getShift()->canHaveAsResponsible($this->getEntityManager(), $this->_getPerson())))
            return $this->error(500, 'This person cannot be a responsible');

        $this->_getShift()->addResponsible(
            $this->getEntityManager(),
            new Responsible(
                $this->_getPerson(),
                $this->getCurrentAcademicYear()
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array()
            )
        );
    }

    public function volunteerAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getAccessToken() || null === $this->_getPerson())
            return $this->error(401, 'The access token is not valid');

        if (null === $this->_getShift())
            return $this->error(500, 'The shift was not found');

        if (!($this->_getShift()->canHaveAsVolunteer($this->getEntityManager(), $this->_getPerson())))
            return $this->error(500, 'This person cannot be a volunteer');

        if ($this->_getShift()->countVolunteers() >= $this->_getShift()->getNbVolunteers()) {
            foreach (array_reverse($this->_getShift()->getVolunteers()) as $volunteer) {
                if ($volunteer->getPerson()->isPraesidium($this->getCurrentAcademicYear())) {
                    $this->_getShift()->removeVolunteer($volunteer);

                    $mailAddress = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail');

                    $mailName = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.mail_name');

                    if (!($language = $volunteer->getPerson()->getLanguage())) {
                        $language = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Language')
                            ->findOneByAbbrev('en');
                    }

                    $mailData = unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('shift.praesidium_removed_mail')
                    );

                    $message = $mailData[$language->getAbbrev()]['content'];
                    $subject = $mailData[$language->getAbbrev()]['subject'];

                    $shiftString = $this->_getShift()->getName() . ' from ' . $this->_getShift()->getStartDate()->format('d/m/Y h:i') . ' to ' . $this->_getShift()->getEndDate()->format('d/m/Y h:i');

                    $mail = new Message();
                    $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
                        ->setFrom($mailAddress, $mailName)
                        ->addTo($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName())
                        ->setSubject($subject);

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    $this->getEntityManager()->remove($volunteer);
                    break;
                }
            }
        }

        $this->_getShift()->addVolunteer(
            $this->getEntityManager(),
            new Volunteer(
                $this->_getPerson()
            )
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array()
            )
        );
    }

    public function signOutAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getAccessToken() || null === $this->_getPerson())
            return $this->error(401, 'The access token is not valid');

        if (null === $this->_getShift())
            return $this->error(500, 'The shift was not found');

        if (!($this->_getShift()->canSignout($this->getEntityManager(), $this->_getPerson())))
            return $this->error(500, 'This person cannot be signed out');

        $remove = $this->_getShift()->removePerson($this->_getPerson());
        if (null !== $remove)
            $this->getEntityManager()->remove($remove);

        /**
         * @TODO If a responsible signs out, and there's another praesidium member signed up as a volunteer, promote him
         */

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array()
            )
        );
    }

    private function _getPerson()
    {
        if (null === $this->getAccessToken())
            return null;

        return $this->getAccessToken()->getPerson($this->getEntityManager());
    }

    private function _getShift()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        return $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getRequest()->getPost('id'));
    }
}
