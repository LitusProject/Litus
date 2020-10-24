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

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use PromBundle\Component\Document\Generator\Bus\Csv as CsvGenerator;
use PromBundle\Entity\Bus;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * BusController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class BusController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus')
                ->findAllBusesByAcademicYear($this->getCurrentAcademicYear()),
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
        $form = $this->getForm('prom_bus_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The bus was successfully added!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_bus',
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
        $bus = $this->getBusEntity();
        if ($bus === null) {
            return new ViewModel();
        }

        $mail = new Message();

        foreach ($bus->getReservedSeatsArray() as $passenger) {
            $passenger->removeBus($bus);
            $mail->addBcc($passenger->getEmail());
        }

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.remove_mail')
        );

        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ busTime }}', $bus->getDepartureTime()->format('d/m/Y H:i'), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addTo($mailData['from'])
            ->setSubject($mailData['subject']);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }

        $this->getEntityManager()->remove($bus);
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'prom_admin_bus',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function exportAction()
    {
        $buses = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->findAllBusesByAcademicYear($this->getCurrentAcademicYear());

        $file = new CsvFile();
        $document = new CsvGenerator($this->getEntityManager(), $buses);
        $document->generateDocument($file);

        $filename = 'PassengerList.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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

    public function viewAction()
    {
        $bus = $this->getBusEntity();
        if ($bus === null) {
            return new ViewModel();
        }

        $passengers = $bus->getReservedSeatsArray();

        return new ViewModel(
            array(
                'passengers' => $passengers,
            )
        );
    }

    /**
     * @return Bus|null
     */
    private function getBusEntity()
    {
        $bus = $this->getEntityById('PromBundle\Entity\Bus');

        if (!($bus instanceof Bus)) {
            $this->flashMessenger()->error(
                'Error',
                'No bus was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_bus',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $bus;
    }
}
