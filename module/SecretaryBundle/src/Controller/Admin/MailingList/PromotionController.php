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

namespace SecretaryBundle\Controller\Admin\MailingList;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Entity\Entries\Academic as AcademicEntry,
    MailBundle\Entity\Entries\External as ExternalEntry,
    MailBundle\Entity\MailingList\Named as NamedList,
    Zend\View\Model\ViewModel;

class PromotionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        $editor = false;
        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        if (!$editor) {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\MailingList\Promotion')
                ->findAllByAdmin($person),
                $this->getParam('page')
            );
        } else {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\MailingList\Promotion')
                ->findAll(),
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'person' => $person,
                'entityManager' => $this->getEntityManager(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
}
