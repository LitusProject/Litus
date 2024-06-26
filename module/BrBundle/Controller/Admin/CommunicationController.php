<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Communication;
use BrBundle\Entity\Company;
use CommonBundle\Entity\User\Person;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * CommunicationController
 */
class CommunicationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $allCommunications = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Communication')
            ->findAll();
        $this->deleteOldCommunications($allCommunications);

        if ($this->getParam('option') === null) {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Communication')
                    ->findBy(array(), array('date' => 'asc')),
                $this->getParam('page')
            );
        } else {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Communication')
                    ->findBy(array('option' => $this->getParam('option')), array('date' => 'asc')),
                $this->getParam('page')
            );
        }

        $config = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.communication_options')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'options'           => $config === 0 ? null : $config,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_communication_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $date = $formData['date'];

                $newCompany = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['companyId']);
                $person = $this->getPersonEntity();
                $newAudience = $formData['audience'];

                $optionKey = $formData['option'];
                $option = unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.communication_options')
                )[$optionKey];

                $oldCommunications = $this->getByDate($date);
                $datesArray = $this->getDatesArray();
                $dateToCheck = $date;

                if (in_array($dateToCheck, $datesArray)) {
                    $optionsArray = $this->getOptionsArray($oldCommunications);
                    if (in_array($optionKey, $optionsArray)) {
                        $oldCommunication = $this->getByOption($optionKey, $oldCommunications)[0];
                        $oldAudience = $oldCommunication->getAudience();
                        $oldCompany = $oldCommunication->getCompany();

                        $this->sendMail($dateToCheck, $person, $option, $newAudience, $newCompany, $oldAudience, $oldCompany);
                    }
                }

                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The communication was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_communication',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $communication = $this->getCommunicationEntity();
        if ($communication == null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($communication);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function getCommunicationEntity()
    {
        $communication = $this->getEntityById('BrBundle\Entity\Communication');

        if (!($communication instanceof Communication)) {
            $this->flashMessenger()->error(
                'Error',
                'No communication was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_communication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $communication;
    }

    private function getDatesArray()
    {
        $dates = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Communication')
            ->findAll();

        $datesArray = array(
            -1 => '',
        );

        foreach ($dates as $date) {
            $datesArray[$date->getId()] = $date->getDate()->format('d/m/Y');
        }

        return $datesArray;
    }

    private function getOptionsArray($test): array
    {
        $optionsArray = array(
            -1 => '',
        );

        foreach ($test as $communication) {
            $optionsArray[$communication->getId()] = $communication->getOption();
        }

        return $optionsArray;
    }

    private function getByDate(string $date)
    {
        $allCommunications = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Communication')
            ->findAll();

        $sameDateArray = array();

        foreach ($allCommunications as $comm) {
            if ($comm->getDate()->format('d/m/Y') === $date) {
                array_push($sameDateArray, $comm);
            }
        }

        return $sameDateArray;
    }

    private function getByOption(string $option, $array)
    {
        $sameOptionsArray = array();

        foreach ($array as $comm) {
            if ($comm->getOption() === $option) {
                array_push($sameOptionsArray, $comm);
            }
        }

        return $sameOptionsArray;
    }

    private function sendMail(string $date, Person $person, string $option, string $newAudience, Company $newCompany, string $oldAudience, Company $oldCompany)
    {
        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.communication_mail');
        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.communication_mail_name');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.communication_mail_body')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(
                str_replace(
                    array('{{ date }}', '{{ person }}', '{{ option }}', '{{ newAudience }}', '{{ newCompany }}', '{{ oldAudience }}', '{{ oldCompany }}'),
                    array($date, $person->getFullName(), $option, $newAudience, $newCompany->getName(), $oldAudience, $oldCompany->getName()),
                    $message
                )
            )
            ->setFrom($mailAddress, $mailName)
            ->addTo($mailAddress, $mailName)
            ->setSubject(
                str_replace(
                    array('{{ date }}'),
                    array($date),
                    $subject
                )
            );

        $this->getMailTransport()->send($mail);
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    private function deleteOldCommunications(array $communications)
    {
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime('-3 day', strtotime($currentDate)));
        foreach ($communications as $comm) {
            $commDate = $comm->getDate()->format('Y-m-d');
            if ($commDate < $currentDate) {
                $this->getEntityManager()->remove($comm);
                $this->getEntityManager()->flush();
            }
        }
    }
}
