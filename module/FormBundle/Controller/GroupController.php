<?php

namespace FormBundle\Controller;

use DateTime;
use FormBundle\Entity\Node\Group;
use FormBundle\Entity\Node\GuestInfo;
use Laminas\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $group = $this->getGroupEntity();
        if ($group === null) {
            return $this->notFoundAction();
        }

        $now = new DateTime();
        if ($now < $group->getStartDate() || $now > $group->getEndDate() || !$group->isActive() || count($group->getForms()) == 0) {
            return new ViewModel(
                array(
                    'message' => 'This form group is currently closed.',
                    'group'   => $group,
                )
            );
        }

        if (!$this->getAuthentication()->isAuthenticated() && !$group->isNonMember()) {
            return new ViewModel(
                array(
                    'message' => 'Please login to view this group.',
                    'group'   => $group,
                )
            );
        }

        $entries = array();
        $firstForm = $group->getForms()[0]->getForm();
        $startForm = $group->getForms()[0]->getForm();

        foreach ($group->getForms() as $form) {
            if ($this->getAuthentication()->isAuthenticated()) {
                $person = $this->getAuthentication()->getPersonObject();
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
            } elseif ($this->isCookieSet()) {
                $guestInfo = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\GuestInfo')
                    ->findOneBySessionId($this->getCookie());

                $guestInfo->renew($this->getRequest());

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
                'group'       => $group,
                'entries'     => $entries,
                'startForm'   => $startForm,
                'isFirstForm' => $startForm->getId() == $firstForm->getId(),
            )
        );
    }

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('FormBundle\Entity\Node\Group');

        if (!($group instanceof Group)) {
            return;
        }

        return $group;
    }

    /**
     * @return boolean
     */
    private function isCookieSet()
    {
        $cookies = $this->getRequest()->getHeader('Cookie');

        return $cookies->offsetExists(GuestInfo::$cookieNamespace);
    }

    /**
     * @return string
     */
    private function getCookie()
    {
        $cookies = $this->getRequest()->getHeader('Cookie');

        return $cookies[GuestInfo::$cookieNamespace];
    }
}
