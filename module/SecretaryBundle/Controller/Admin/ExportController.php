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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    SecretaryBundle\Component\Document\Generator\Registration as CsvGenerator,
    SecretaryBundle\Form\Admin\Export\Export as ExportForm,
    Zend\View\Model\ViewModel;

/**
 * ExportController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ExportController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function exportAction()
    {
        $form = $this->getForm('secretary_export_export');
        $form->setAttribute(
             'action',
             $this->url()->fromRoute(
                 'secretary_admin_export', array('action' => 'download')
             )
         );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function downloadAction()
    {
        $form = $this->getForm('secretary_export_export');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academicYear = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear')
                    ->findOneById($formData['academic_year']);

                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                $exportFile = new CsvFile();
                $csvGenerator = new CsvGenerator($this->getEntityManager(), $organization, $academicYear);
                $csvGenerator->generateDocument($exportFile);

                $this->getResponse()->getHeaders()
                    ->addHeaders(
                    array(
                        'Content-Disposition' => 'attachment; filename="members_' . strtolower($organization->getName()) . '_' . $academicYear->getCode() . '.csv"',
                        'Content-Type' => 'text/csv',
                    )
                );

                return new ViewModel(
                    array(
                        'result' => $exportFile->getContent(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => null,
            )
        );
    }
}
