<?php

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

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
                'university'   => UniversityStatus::$possibleStatuses,
                'organization' => OrganizationStatus::$possibleStatuses,
            )
        );
    }

    public function sendAction()
    {
        $type = $this->getType();
        if ($type === null) {
            return new ViewModel();
        }

        if ($type == 'organization') {
            $status = $this->getOrganizationStatus();
            if ($status === null) {
                return new ViewModel();
            }

            $statuses = OrganizationStatus::$possibleStatuses;
        } else {
            $status = $this->getUniversityStatus();
            if ($status === null) {
                return new ViewModel();
            }

            $statuses = UniversityStatus::$possibleStatuses;
        }

        $form = $this->getForm('mail_group_mail');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $body = $formData['message'];
                if ($formData['test']) {
                    $body = 'This email would have been sent to the group "' . $type . ' - ' . $status . '":' . PHP_EOL . PHP_EOL . $body;
                }

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody($body)
                    ->setFrom($formData['from'], $formData['name'])
                    ->setSubject($formData['subject']);

                if ($type == 'organization') {
                    $people = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Status\Organization')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear(false));
                } else {
                    $people = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Status\University')
                        ->findAllByStatus($status, $this->getCurrentAcademicYear(false));
                }

                $mail->addTo($formData['from'], $formData['name']);

                if (!$formData['test']) {
                    $addresses = array();
                    foreach ($people as $person) {
                        if ($person->getPerson()->getEmail() === null) {
                            continue;
                        }

                        $addresses[$person->getPerson()->getEmail()] = array(
                            'address' => $person->getPerson()->getEmail(),
                            'name'    => $person->getPerson()->getFullName(),
                        );
                    }

                    $i = 0;
                    foreach ($addresses as $address) {
                        $mail->addBcc($address['address'], $address['name']);
                        $i++;

                        if ($i == 500) {
                            $i = 0;

                            if (getenv('APPLICATION_ENV') != 'development') {
                                $this->getMailTransport()->send($mail);
                            }

                            $mail->setBcc(array());
                        }
                    }
                }

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_group',
                    array(
                        'action' => 'groups',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'type'   => $type,
                'status' => $statuses[$status],
                'form'   => $form,
            )
        );
    }

    /**
     * @return string|null
     */
    private function getType()
    {
        $type = $this->getParam('type', '');

        if ($type != 'organization' && $type != 'university') {
            $this->flashMessenger()->error(
                'Error',
                'No type was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $type;
    }

    /**
     * @return string|null
     */
    private function getUniversityStatus()
    {
        $status = $this->getParam('group', '');

        if (!array_key_exists($status, UniversityStatus::$possibleStatuses)) {
            $this->flashMessenger()->error(
                'Error',
                'No university status was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups',
                )
            );

            return;
        }

        return $status;
    }

    /**
     * @return string|null
     */
    private function getOrganizationStatus()
    {
        $status = $this->getParam('group', '');

        if (!array_key_exists($status, OrganizationStatus::$possibleStatuses)) {
            $this->flashMessenger()->error(
                'Error',
                'No organization status was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_group',
                array(
                    'action' => 'groups',
                )
            );

            return;
        }

        return $status;
    }
}
