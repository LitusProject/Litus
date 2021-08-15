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
//                echo json_encode($formData);
//                die(1);
                $date = $formData['date'];
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['companyId']);
//                echo json_encode($company);
//                die(1);
                $audience = $formData['audience'];

                $this->checkDuplicateDate($date, $company, $audience);

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

    private function sendMail(string $date, Company $company, string $audience)
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
                    array('{{ date }}', '{{ companyName }}', '{{ audience }}'),
                    array($date, $company->getName(), $audience),
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

    private function checkDuplicateDate(string $date, Company $company, string $audience)
    {
        $datesArray = $this->getDatesArray();
        $dateToCheck = $date;
        if (in_array($dateToCheck, $datesArray)) {
            $this->sendMail($dateToCheck, $company, $audience);
        }
    }
}
