<?php

namespace Run;

use \Run\Form\Group\Add as AddForm;

use \Litus\Entity\Sport\Group;
use \Litus\Entity\Sport\Runner;

class GroupController extends \Litus\Controller\Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_redirect('add');
    }

    public function addAction()
    {
        $allMembers = array(
            'one', 'two', 'three', 'four', 'five'
        );

        $form = new AddForm($allMembers);

        $this->view->form = $form;
        $this->view->groupCreated = false;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $createGroup = true;
                foreach ($allMembers as $memberNb) {
                    if ('' != $formData['university_identification_' . $memberNb]) {
                        if (
                            '' == $formData['first_name_' . $memberNb] &&
                            '' == $formData['last_name_' . $memberNb]
                        ) {
                            if (true === $createGroup)
                                $createGroup = false;
                        }
                    } else {
                        $memberNbKey = array_keys($allMembers, $memberNb);
                        unset(
                            $allMembers[$memberNbKey[0]]
                        );
                    }
                }

                if ($createGroup) {
                    $newGroup = new Group(
                        $formData['group_name'],
                        array(
                            $formData['happy_hour_one'],
                            $formData['happy_hour_two']
                        )
                    );

                    $groupMembers = array();
                    foreach ($allMembers as $memberNb) {
                        $repositoryCheck = $this->getEntityManager()
                            ->getRepository('Litus\Entity\Sport\Runner')
                            ->find($formData['university_identification_' . $memberNb]);

                        if (null === $repositoryCheck) {
                            $newRunner = new Runner(
                                $formData['university_identification_' . $memberNb],
                                $formData['first_name_' . $memberNb],
                                $formData['last_name_' . $memberNb]
                            );
                            $newRunner->setGroup($newGroup);

                            $groupMembers[] = $newRunner;
                        } else {
                            if (null === $repositoryCheck->getGroup()) {
                                $repositoryCheck->setGroup($newGroup);
                                $groupMembers[] = $repositoryCheck;
                            }
                        }

                    }

                    if (0 != count($groupMembers)) {
                        $newGroup->setMembers($groupMembers);

                        $this->getEntityManager()->persist($newGroup);

                        $this->view->groupCreated = true;
                        $this->view->groupMembers = $groupMembers;
                    }
                }
            }
        }
    }
}
