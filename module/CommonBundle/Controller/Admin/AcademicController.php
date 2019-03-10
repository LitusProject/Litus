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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Component\Socket\Sale\Printer;
use Doctrine\ORM\Query;
use Zend\View\Model\ViewModel;

/**
 * AcademicController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */
class AcademicController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = null;
        if ($this->getParam('field') !== null) {
            $academics = $this->search();
            if ($academics === null) {
                return new ViewModel();
            }

            $paginator = $this->paginator()->createFromQuery(
                $academics,
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
            $paginator = $this->paginator()->createFromEntity(
                'CommonBundle\Entity\User\Person\Academic',
                $this->getParam('page'),
                array(
                    'canLogin' => 'true',
                ),
                array(
                    'username' => 'ASC',
                )
            );
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('\CommonBundle\Form\Admin\Academic\Add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                $academic = $form->hydrateObject();

                $this->getEntityManager()->persist($academic);

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    !$formData['activation_code']
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The academic was successfully created!'
                );

                $this->redirect()->toRoute(
                    'common_admin_academic',
                    array(
                        'action' => 'add',
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

    public function editAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            '\CommonBundle\Form\Admin\Academic\Edit',
            array(
                'academic' => $academic
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The academic was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'common_admin_academic',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'     => $form,
                'academic' => $academic,
            )
        );
    }

    public function activateAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $academic->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false
        );

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The academic was successfully activated!'
        );

        $this->redirect()->toRoute(
            'common_admin_academic',
            array(
                'action' => 'edit',
                'id'     => $academic->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $sessions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Session')
            ->findByPerson($academic->getId());

        foreach ($sessions as $session) {
            $session->deactivate();
        }

        $academic->disableLogin();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array('status' => 'success'),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $academics = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults(10)
            ->getResult();

        $result = array();
        foreach ($academics as $academic) {
            $identification = $academic->getUniversityIdentification() ? $academic->getUniversityIdentification() : $academic->getUserName();

            $item = (object) array();
            $item->id = $academic->getId();
            $item->name = $academic->getFullName();
            $item->universityIdentification = $identification;
            $item->value = $academic->getFullName() . ' - ' . $identification;
            $result[] = $item;
        }

        $barcodes = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findAllByBarcode($this->getParam('string'));

        if (count($barcodes) > 10) {
            $barcodes = array_slice(0, 10);
        }

        $barcodePeople = array();
        foreach ($barcodes as $barcode) {
            $person = $barcode->getPerson();
            if ($person->canLogin()) {
                $barcodePeople[] = $person;
            }
        }

        foreach ($barcodePeople as $person) {
            $barcode = $person->getBarcode()->getBarcode();
            $universityIdentification = $person->getUniversityIdentification() ? $person->getUniversityIdentification() : $person->getUserName();

            $item = (object) array();
            $item->id = $person->getId();
            $item->name = $person->getFullName();
            $item->universityIdentification = $universityIdentification;
            $item->value = $person->getFullName() . ' - ' . $barcode;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $academics = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($academics as $academic) {
            if ($academic->canLogin()) {
                $item = (object) array();
                $item->id = $academic->getId();
                $item->username = $academic->getUsername();
                $item->universityIdentification = ($academic->getUniversityIdentification() ?? '');
                $item->fullName = $academic->getFullName();
                $item->email = $academic->getEmail();

                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUsernameQuery($this->getParam('string'));
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByNameQuery($this->getParam('string'));
            case 'university_identification':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUniversityIdentificationQuery($this->getParam('string'));
        }
    }

    public function printAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        Printer::membershipCard(
            $this->getEntityManager(),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.card_printer'),
            $academic,
            $this->getCurrentAcademicYear()
        );

        return new ViewModel(
            array(
                'result' => array('status' => 'success'),
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_academic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academic;
    }
}
