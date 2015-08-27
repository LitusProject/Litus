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

namespace ShopBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * ShopController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ShopController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        //TODO
        $canReserve = true;

        return new ViewModel(
            array(
                'canReserve' => $canReserve,
            )
        );
    }

    public function reserveAction()
    {
        $reserveForm = $this->getForm('shop_shop_reserve');
        $canReserve = true;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $reserveForm->setData($formData);

            if ($reserveForm->isValid()) {
                $reservation = $reserveForm->hydrateObject();
                $reservation->setPerson($this->getPersonEntity());
                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The reservation was successfully made!'
                );

                $this->redirect()->toRoute(
                    'shop',
                    array(
                        'action' => 'reserve',
                    )
                );

                return new ViewModel();
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'An error occurred while processing your reservation!'
                );
            }
        }

        return new ViewModel(
            array(
                'canReserve' => $canReserve,
                'form' => $reserveForm,
            )
        );
    }

    public function reservationsAction()
    {
        //TODO
        $canReserve = true;

        $reservations = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getAllCurrentReservationsByPersonId($this->getPersonEntity());

        return new ViewModel(
            array(
                'canReserve' => $canReserve,
                'reservations' => $reservations,
            )
        );
    }

    public function deleteReservationAction()
    {
        if ($reservation = $this->getEntityById('ShopBundle\Entity\Reservation')) {
            $this->getEntityManager()->remove($reservation);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->success("Success", "Your reservation was successfully cancelled");
        } else {
            $this->flashMessenger()->error("Error", "An error occurred while trying to cancel your reservation");
        }

        $this->redirect()->toRoute(
            'shop',
            array(
                'action' => 'reservations',
            )
        );

        return new ViewModel();
    }

    /**
	 * @return \CommonBundle\Entity\User\Person|null
	 */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }
}
