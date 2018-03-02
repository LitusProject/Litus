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

namespace PromBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    PromBundle\Entity\Bus\ReservationCode,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

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
        $form = $this->getForm('prom_reservationCode_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                for ($i = 0; $i < $formData['nb_codes']; $i++) {
                    $newCode = new ReservationCode($this->getCurrentAcademicYear());
                    $this->getEntityManager()->persist($newCode);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The codes were successfully generated!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_code',
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

    public function expireAction()
    {
        if (!($code = $this->getReservationCodeEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($code);
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'prom_admin_code',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
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
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="codes.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function viewAction()
    {
        if (!($code = $this->getReservationCodeEntity())) {
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
}
