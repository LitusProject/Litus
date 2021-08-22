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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Communication;
use BrBundle\Entity\Company;
use CommonBundle\Entity\User\Person;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * CommunicationController
 * @author Stan Cardinaels <stan.cardinaels@vtk.be>
 */
class CommunicationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
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

                if (in_array($dateToCheck, $datesArray) ) {
                    $optionsArray = $this->getOptionsArray($oldCommunications);
                    if (in_array($optionKey, $optionsArray)) {
                        $oldCommunication = $oldCommunications[0];
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

        foreach($allCommunications as $comm) {
            if ($comm->getDate()->format('d/m/Y') === $date) {
                array_push($sameDateArray, $comm);
            }
        }

        return $sameDateArray;
    }

    private function sendMail(string $date, Person $person, string $option, string $newAudience,Company $newCompany, string $oldAudience, Company $oldCompany)
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

}
