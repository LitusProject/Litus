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

namespace SportBundle\Controller\Run;

use SportBundle\Entity\Group,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GroupController extends \SportBundle\Component\Controller\RunController
{
    public function addAction()
    {
        $form = $this->getForm('sport_group_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            foreach (Group::$allMembers as $memberNb) {
                $memberData = $formData['user_' . $memberNb];

                if (
                    '' != $memberData['university_identification']
                        && !isset($memberData['first_name'])
                        && !isset($memberData['last_name'])
                ) {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUniversityIdentification($memberData['university_identification']);

                    $memberData['first_name'] = $academic->getFirstName();
                    $memberData['last_name'] = $academic->getLastName();
                }
            }
            $form->setData($formData);

            if ($form->isValid()) {
                $group = $form->hydrateObject(
                    new Group($this->getCurrentAcademicYear())
                );

                if (null !== $group) {
                    $this->getEntityManager()->persist($group);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The group was successfully created!'
                    );

                    $this->redirect()->toRoute(
                        'sport_run_index',
                        array(
                            'action' => 'index',
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function getNameAction()
    {
        $this->initAjax();

        if (8 == strlen($this->getParam('university_identification'))) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($this->getParam('university_identification'));

            if (null !== $academic) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $academic->getFirstName(),
                            'lastName' => $academic->getLastName(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array(),
            )
        );
    }
}
