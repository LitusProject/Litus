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

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \MailBundle\Component\Controller\AdminController
{
    public function groupsAction()
    {
        return new ViewModel(
            array(
                'university' => UniversityStatus::$possibleStatuses,
                'organization' => OrganizationStatus::$possibleStatuses,
            )
        );
    }

    public function sendAction()
    {
        if (!($type = $this->_getType()))
            return new ViewModel();

        if ('organization' == $type) {
            if (!($status = $this->_getOrganizationStatus()))
                return new ViewModel();
            $statuses = OrganizationStatus::$possibleStatuses;
        } else {
            if (!($status = $this->_getUniversityStatus()))
                return new ViewModel();
            $statuses = UniversityStatus::$possibleStatuses;
        }

        $form = $this->getForm('mail_group_mail');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData($formData);

                $body = $formData['message'];
                if ($formData['test'])
                    $body = 'This email would have been sent to the group "' . $type . ' - ' . $status . '":' . PHP_EOL . PHP_EOL . $body;

                $mail = new Message();
                $mail->setBody($body)
                    ->setFrom($formData['from'], $formData['name'])
                    ->setSubject($formData['subject']);

                if ('organization' == $type) {
                    $people = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Status\Organization')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear(false));
                } else {
                    $people = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Status\University')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear(false));
                }

                $mail->addTo($mailAddress, $mailName);

                if (!$formData['test']) {
                    $addresses = array();
                    foreach ($people as $person) {
                        if (null === $person->getPerson()->getEmail())
                            continue;

                        $addresses[$person->getPerson()->getEmail()] = array(
                            'address' => $person->getPerson()->getEmail(),
                            'name'    => $person->getPerson()->getFullName(),
                        );
                    }

                    $i = 0;
                    foreach ($addresses as $address) {
                        $mail->addBcc($address['address'], $address['name']);
                        $i++;

                        if (500 == $i) {
                            $i = 0;

                            if ('development' != getenv('APPLICATION_ENV'))
                                $this->getMailTransport()->send($mail);

                            $mail->setBcc(array());
                        }
                    }
                }

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_group',
                    array(
                        'action' => 'groups'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'type' => $type,
                'status' => $statuses[$status],
                'form' => $form,
            )
        );
    }

    private function _getType()
    {
        if (null === $this->getParam('type')) {
            $this->flashMessenger()->error(
                'Error',
                'No university status given to send a mail to!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        };

        $type = $this->getParam('type');

        if ('organization' != $type && 'university' != $type) {
            $this->flashMessenger()->error(
                'Error',
                'No university status given to send a mail to!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        }

        return $type;
    }

    private function _getUniversityStatus()
    {
        if (null === $this->getParam('group')) {
            $this->flashMessenger()->error(
                'Error',
                'No university status given to send a mail to!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        };

        $status = $this->getParam('group');

        if (!array_key_exists($status, UniversityStatus::$possibleStatuses)) {
            $this->flashMessenger()->error(
                'Error',
                'The given university status was not valid!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        }

        return $status;
    }

    private function _getOrganizationStatus()
    {
        if (null === $this->getParam('group')) {
            $this->flashMessenger()->error(
                'Error',
                'No organization status given to send a mail to!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        };

        $status = $this->getParam('group');

        if (!array_key_exists($status, OrganizationStatus::$possibleStatuses)) {
            $this->flashMessenger()->error(
                'Error',
                'The given organization status was not valid!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups'
                )
            );

            return;
        }

        return $status;
    }
}
