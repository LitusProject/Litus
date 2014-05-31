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

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
    BrBundle\Entity\Company\Request\RequestVacancy,
    BrBundle\Form\Corporate\Vacancy\Add as AddForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * VacancyController
 *
 * @author Incalza Dario <dario.incalza@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByCompanyAndTypeQuery($person->getCompany(), 'vacancy');

        $paginator = $this->paginator()->createFromQuery(
            $query,
            $this->getParam('page')
        );

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath' => $logoPath,
            )
        );
    }

    public function viewAction()
    {
        $vacancy = $this->_getVacancy();

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'vacancy' => $vacancy,
                'logoPath' => $logoPath,
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getAuthentication()->getPersonObject();

                $job = new Job(
                    $formData['job_name'],
                    $formData['description'],
                    $formData['benefits'],
                    $formData['profile'],
                    $formData['contact'],
                    $formData['city'],
                    $contact->getCompany(),
                    'vacancy',
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['sector']
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestVacancy($job, 'add', $contact);

                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The request has been sent to our administrators for approval.'
                    )
                );

                $this->redirect()->toRoute(
                    'br_corporate_vacancy',
                    array(
                        'action' => 'overview',
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
        $vacancy = $this->_getVacancy();

        $contact = $this->getAuthentication()->getPersonObject();

        $request = new RequestVacancy($vacancy, 'delete', $contact);

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The request has been sent to our administrators for approval.'
            )
        );

        $this->redirect()->toRoute(
            'br_corporate_vacancy',
            array(
                'action' => 'overview',
            )
        );

        return new ViewModel();
    }

    private function _getVacancy()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the vacancy!'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $vacancy = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('vacancy', $this->getParam('id'));

        if (null === $vacancy) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No vacancy with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $vacancy;
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }
}
