<?php

namespace PromBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use PromBundle\Entity\Bus\ReservationCode;
use PromBundle\Entity\Bus\ReservationCode\Academic as AcademicCode;
use PromBundle\Entity\Bus\ReservationCode\External as ExternalCode;

/**
 * CodeController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CodeController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                ->getAllCodesByAcademicYear($this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $academicForm = $this->getForm('prom_reservationCode_academic');
        $externalForm = $this->getForm('prom_reservationCode_external');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $academicForm->setData($formData);
            $externalForm->setData($formData);

            $codes = array();

            if (isset($formData['academic_add']) && $academicForm->isValid()) {
                for ($i = 0; $i < $formData['number_tickets']; $i++) {
                    $codes[] = $academicForm->hydrateObject(
                        new AcademicCode($this->getCurrentAcademicYear())
                    );
                }
            } elseif (isset($formData['external_add']) && $externalForm->isValid()) {
                for ($i = 0; $i < $formData['number_tickets']; $i++) {
                    $codes[] = $externalForm->hydrateObject(
                        new ExternalCode($this->getCurrentAcademicYear())
                    );
                }
            }

            if ($codes !== array()) {
                foreach ($codes as $code) {
                    $this->getEntityManager()->persist($code);
                }
                $this->getEntityManager()->flush();

                $this->sendReservationCodeMail($codes);

                $this->flashMessenger()->success(
                    'Success',
                    'The bus code was successfully created!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_code',
                    array(
                        'action' => 'add',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academicForm' => $academicForm,
                'externalForm' => $externalForm,
            )
        );
    }

    public function expireAction()
    {
        $this->initAjax();

        $code = $this->getReservationCodeEntity();
        if ($code === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($code);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function exportAction()
    {
        $entries = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getAllCodesByAcademicYear($this->getCurrentAcademicYear());

        $file = new CsvFile();
        $heading = array('Code');

        $results = array();
        foreach ($entries as $entry) {
            $results[] = array(
                $entry->getCode(),
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="codes.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $codes = $this->search($numResults);

        $result = array();
        foreach ($codes as $code) {
            $item = (object) array();
            $item->id = $code->getId();
            $item->code = $code->getCode();
            $item->firstName = $code->getFirstName();
            $item->lastName = $code->getLastName();
            $item->used = $code->isUsed();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function mailAction()
    {
        $this->initAjax();

        $code = $this->getReservationCodeEntity();
        $this->sendReservationCodeMail($code);

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function warningMailAction()
    {
        $this->initAjax();

        $this->sendWarningMail();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search($numResults)
    {
        switch ($this->getParam('field')) {
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllByCodeQuery($this->getParam('string'))
                    ->setMaxResults($numResults)
                    ->getResult();
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllByUniversityIdentificationQuery($this->getParam('string'))
                    ->setMaxResults($numResults)
                    ->getResult();
            case 'name':
                $academics = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllAcademicByNameQuery($this->getParam('string'))
                    ->setMaxResults($numResults)
                    ->getResult();
                $externals = $this->getEntityManager()
                    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
                    ->findAllExternalByNameQuery($this->getParam('string'))
                    ->setMaxResults($numResults)
                    ->getResult();

                $result = array_merge($academics, $externals);

                return array_slice($result, 0, $numResults);
        }
    }

    public function viewAction()
    {
        $code = $this->getReservationCodeEntity();
        if ($code === null) {
            return new ViewModel();
        }

        if ($code->isUsed()) {
            $passenger = $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\Passenger')
                ->findPassengerByCode($code);
        } else {
            $passenger = null;
        }

        return new ViewModel(
            array(
                'passenger' => $passenger[0],
                'code'      => $code,
            )
        );
    }

    /**
     * @return ReservationCode|null
     */
    private function getReservationCodeEntity()
    {
        $code = $this->getEntityById('PromBundle\Entity\Bus\ReservationCode');

        if (!($code instanceof ReservationCode)) {
            $this->flashMessenger()->error(
                'Error',
                'No code was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_code',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $code;
    }

    private function sendReservationCodeMail($codes)
    {
        if (!is_array($codes)) {
            $codes = array($codes);
        }

        if (count($codes) == 0) {
            return;
        }

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.reservation_mail')
        );

        $body = $mailData['body'];
        $body = str_replace('{{ firstName }}', $codes[0]->getFirstName(), $body);
        $body = str_replace('{{ lastName }}', $codes[0]->getLastName(), $body);

        preg_match('/.*{{ reservationCode }}.*\n/', $body, $matches);

        $codesString = '';
        foreach ($codes as $code) {
            $codesString .= str_replace('{{ reservationCode }}', $code->getCode(), $matches[0]);
        }
        $body = preg_replace('/( )*{{ reservationCode }}.*\n/', $codesString, $body);

        $mail = new Message();
        $mail->addTo($codes[0]->getEmail())
            ->setEncoding('UTF-8')
            ->setBody($body)
            ->setFrom($mailData['from'])
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    private function sendWarningMail()
    {
        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.reservation_opening_warning')
        );

        $mail = new Message();
        $mail->addTo($mailData['from'])
            ->setEncoding('UTF-8')
            ->setFrom($mailData['from'])
            ->setBody($mailData['body'])
            ->setSubject($mailData['subject']);

        $codes = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->findAll();

        $addresses = array();
        foreach ($codes as $code) {
            $addresses[] = $code->getEmail();
        }

        foreach ($addresses as $address) {
            $mail->addBcc($address);
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
