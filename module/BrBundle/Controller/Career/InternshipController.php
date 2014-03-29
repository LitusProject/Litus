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

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company,
    BrBundle\Form\Career\Search\Internship as InternshipSearchForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * InternshipController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class InternshipController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $internshipSearchForm = new InternshipSearchForm();

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeQuery('internship');

        $formData = $this->getRequest()->getQuery();
        $internshipSearchForm->setData($formData);

        if ($internshipSearchForm->isValid()) {
            $formData = $internshipSearchForm->getFormData($formData);

            $repository = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job');

            if ('all' != $formData['sector']) {
                if ('company' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeAndSectorQuery('internship', $formData['sector']);
                } elseif ('internship' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeAndSectorByJobNameQuery('internship', $formData['sector']);
                } elseif ('mostRecent' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeAndSectorByDateQuery('internship', $formData['sector']);
                }
            } else {
                if ('internship' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeByJobNameQuery('internship');
                } elseif ('mostRecent' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeByDateQuery('internship');
                }
            }
        }

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
                'internshipSearchForm' => $internshipSearchForm,
            )
        );
    }

    public function viewAction()
    {
        $internship = $this->_getInternship();

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'internship' => $internship,
                'logoPath' => $logoPath,
            )
        );
    }

    private function _getInternship()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the internship!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_internship',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $internship = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('internship', $this->getParam('id'));

        if (null === $internship) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No internship with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_internship',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $internship;
    }
}
