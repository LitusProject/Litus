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

namespace CudiBundle\Controller\Admin\Sale\Session;

use CommonBundle\Component\Controller\Exception\RuntimeException,
    CudiBundle\Entity\Sale\Session,
    CudiBundle\Entity\Sale\Session\Restriction,
    CudiBundle\Entity\Sale\Session\Restriction\Name as NameRestriction,
    CudiBundle\Entity\Sale\Session\Restriction\Study as StudyRestriction,
    CudiBundle\Entity\Sale\Session\Restriction\Year as YearRestriction,
    Zend\View\Model\ViewModel;

/**
 * RestrictionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RestrictionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($session = $this->getSessionEntity())) {
            return new ViewModel();
        }

        $restrictions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\Restriction')
            ->findBySession($session);

        $form = $this->getForm('cudi_sale_session_restriction_add', array(
            'session' => $session,
        ));

        if ($this->getRequest()->isPost() && $session->isOpen()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if ('name' == $formData['type']) {
                    $restriction = new NameRestriction($session, $formData['start_value_name'], $formData['end_value_name']);
                } elseif ('year' == $formData['type']) {
                    $restriction = new YearRestriction($session, $formData['start_value_year'], $formData['end_value_year']);
                } elseif ('study' == $formData['type']) {
                    $restriction = new StudyRestriction($session);

                    foreach ($formData['value_study'] as $id) {
                        $study = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Study')
                            ->findOneById($id);

                        $restriction->addStudy($study);
                    }
                } else {
                    throw new RuntimeException('Unsupported restriction type');
                }

                $this->getEntityManager()->persist($restriction);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The restriction was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_restriction',
                    array(
                        'action' => 'manage',
                        'id' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'restrictions' => $restrictions,
                'session' => $session,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($restriction = $this->getRestrictionEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($restriction);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Session|null
     */
    private function getSessionEntity()
    {
        $session = $this->getEntityById('CudiBundle\Entity\Sale\Session');

        if (!($session instanceof Session)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $session;
    }

    /**
     * @return Restriction|null
     */
    private function getRestrictionEntity()
    {
        $restriction = $this->getEntityById('CudiBundle\Entity\Sale\Session\Restriction');

        if (!($restriction instanceof Restriction)) {
            $this->flashMessenger()->error(
                'Error',
                'No restriction was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $restriction;
    }
}
