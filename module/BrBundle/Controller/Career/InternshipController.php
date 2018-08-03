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

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
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
        $internshipSearchForm = $this->getForm('br_career_search_internship');

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeByDateQuery('internship');

        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();
            $internshipSearchForm->setData($formData);

            if ($internshipSearchForm->isValid()) {
                $formData = $internshipSearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job');

                $sector = $formData['sector'] == 'all' ? null : $formData['sector'];
                $location = $formData['location'] == 'all' ? null : $formData['location'];
                $master = $formData['master'] == 'all' ? null : $formData['master'];

                if ('company' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeQuery('internship', $sector, $location, $master);
                } elseif ('vacancy' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeSortedByJobNameQuery('internship', $sector, $location, $master);
                } elseif ('mostRecent' == $formData['searchType']) {
                    $query = $repository->findAllActiveByTypeSortedByDateQuery('internship', $sector, $location, $master);
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
                'paginator'            => $paginator,
                'paginationControl'    => $this->paginator()->createControl(true),
                'logoPath'             => $logoPath,
                'internshipSearchForm' => $internshipSearchForm,
            )
        );
    }

    public function viewAction()
    {
        if (!($internship = $this->getInternshipEntity())) {
            return new ViewModel();
        }

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'internship' => $internship,
                'logoPath'   => $logoPath,
            )
        );
    }

    /**
     * @return Job|null
     */
    private function getInternshipEntity()
    {
        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('internship', $this->getParam('id', 0));

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No job was found!'
            );

            $this->redirect()->toRoute(
                'br_career_internship',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $job;
    }
}
