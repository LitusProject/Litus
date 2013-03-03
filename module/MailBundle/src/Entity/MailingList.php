<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList")
 * @ORM\Table(name="mail.lists")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "named"="MailBundle\Entity\MailingList\Named",
 * })
 */
abstract class MailingList
{
    /**
     * @var int The list's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function canBeEditedBy($person, $entityManager, $editAdmin)
    {
        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor')
                return true;
        }

        $adminMap = $entityManager
            ->getRepository('MailBundle\Entity\MailingList\AdminMap')
            ->findOneBy(
                array(
                    'list' => $this,
                    'academic' => $person,
                )
            );

        if (!$adminMap) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to manage the given list!'
                )
            );

            $this->redirect()->toRoute(
                'admin_mail_list',
                array(
                    'action' => 'manage'
                )
            );

            return false;
        }

        if ($adminEdit && !$adminMap->isAdminEdit()) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to manage the admins for the given list!'
                )
            );

            $this->redirect()->toRoute(
                'admin_mail_list',
                array(
                    'action' => 'manage'
                )
            );

            return false;
        }

        return true;
    }
}
