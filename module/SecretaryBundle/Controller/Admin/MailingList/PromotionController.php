<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
    MailBundle\Entity\Entry\Academic as AcademicEntry,
    MailBundle\Entity\Entry\External as ExternalEntry,
    MailBundle\Entity\MailingList\Named as NamedList,
    SecretaryBundle\Entity\Promotion,
    SecretaryBundle\Entity\MailingList\Promotion as PromotionList,
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

    public function generateAction()
    {
        $academicYear = $this->getCurrentAcademicYear();

        $existingList = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\MailingList\Promotion')
            ->findOneByAcademicYear($academicYear);

        if (!$existingList) {

            // Create the promotion if necessary
            $promotion = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Promotion')
                ->findOneByAcademicYear($academicYear);

            if (!$promotion) {
                $promotion = new Promotion($academicYear);

                $this->getEntityManager()->persist($promotion);
            }

            // Create the promotion list
            $list = new PromotionList($promotion);

            $this->getEntityManager()->persist($list);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::SUCCESS,
                    'SUCCES',
                    'The promotion list was succesfully created!'
                )
            );
        } else {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'The promotion list for this year already exists! Remove the existing one to generate a new one.'
                )
            );
        }

        $this->redirect()->toRoute(
            'secretary_admin_mail_promotion',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }
}
