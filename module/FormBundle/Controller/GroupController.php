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

namespace FormBundle\Controller;

use DateTime,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($group = $this->_getGroup()))
            return $this->notFoundAction();

        $now = new DateTime();
        if ($now < $group->getStartDate() || $now > $group->getEndDate() || !$group->isActive() || sizeof($group->getForms()) == 0) {
            return new ViewModel(
                array(
                    'message'   => 'This form group is currently closed.',
                    'group'     => $group,
                )
            );
        }

        if (!$this->getAuthentication()->isAuthenticated() && !$group->isNonMember()) {
            return new ViewModel(
                array(
                    'message'   => 'Please login to view this group.',
                    'group'     => $group,
                )
            );
        }

        $entries = array();
        $firstForm = $group->getForms()[0]->getForm();
        $startForm = $group->getForms()[0]->getForm();

        foreach ($group->getForms() as $form) {
            $person = $this->getAuthentication()->getPersonObject();
            $cookie = $this->getRequest()->getHeader('Cookie');

            if (null !== $person) {
                $entries[$form->getForm()->getId()] = array(
                    'entry' => current(
                        $this->getEntityManager()
                            ->getRepository('FormBundle\Entity\Node\Entry')
                            ->findAllByFormAndPerson($form->getForm(), $person)
                    ),
                    'draft' => $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findDraftVersionByFormAndPerson($form->getForm(), $person) !== null,
                );

                if ($entries[$form->getForm()->getId()]['entry']) {
                    $startForm = $form->getForm();
                }
            } elseif (isset($cookie['LITUS_form'])) {
                $guestInfo = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\GuestInfo')
                    ->findOneBySessionId($cookie['LITUS_form']);

                $guestInfo->renew();

                $entries[$form->getForm()->getId()] = array(
                    'entry' => current(
                        $this->getEntityManager()
                            ->getRepository('FormBundle\Entity\Node\Entry')
                            ->findAllByFormAndGuestInfo($form->getForm(), $guestInfo)
                    ),
                    'draft' => $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findDraftVersionByFormAndGuestInfo($form->getForm(), $guestInfo) !== null,
                );

                if ($entries[$form->getForm()->getId()]['entry']) {
                    $startForm = $form->getForm();
                }
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'entries' => $entries,
                'startForm' => $startForm,
                'isFirstForm' => $startForm->getId() == $firstForm->getId(),
            )
        );
    }

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            return;
        }

        return $group;
    }
}
