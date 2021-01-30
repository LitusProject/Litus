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

namespace CudiBundle\Controller;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Deal;
use CudiBundle\Entity\Retail;
use Laminas\View\Model\ViewModel;

/**
 * RetailController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class RetailController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return $this->notFoundAction();
        }
        $bookSearchForm = $this->getForm('cudi_retail_search_book', array('language' => $this->getLanguage()));
//        print("here");die();

        $myDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByBuyerQuery($academic->getId())->getResult();

        $retails = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Retail')
            ->FindAllQuery()->getResult();

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();


                $bookSearchForm->setData($formData);
                if ($formData['search_string'] === '') {
                    $retails = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Retail')
                        ->findAllQuery()->getResult();
                }
                elseif ($bookSearchForm->isValid()) {
                    $formData = $bookSearchForm->getData();

                    $retails = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Retail')
                        ->findAllByTitle($formData['search_string']);

                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'The given search query was invalid!'
                    );
                }
        }

        foreach ($myDeals as $deal) {
            if (in_array($deal->getRetail(), $retails)) {
                unset($retails[array_keys($retails, $deal->getRetail())[0]]);
            }
        }

        return new ViewModel(
            array(
                'bookSearchForm'   => $bookSearchForm,
                'myDeals'          => $myDeals,
                'searchResults'    => $retails,
                'entityManager'    => $this->getEntityManager(),
            )
        );
    }

    public function dealAction()
    {

        $this->initAjax();

        $retail = $this->getRetailEntity();
        if ($retail === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }


        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }


        $enquiredAcademics = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByRetail($retail->getId());

        $alreadyEnquired = in_array($academic, $enquiredAcademics);

        if ($alreadyEnquired === true) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }


        new Deal($retail, $academic);

        // TODO: Mail uitsturen wanneer enquiry gemaakt wordt!

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
    }

    /**
     * @return Retail
     */
    private function getRetailEntity()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return;
        }

        if ($this->getParam('id') === null || !is_numeric($this->getParam('id'))) {
            return;
        }

        $retail = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Retail')
            ->findOneById($this->getParam('id'));

        if ($retail->getPerson() !== $academic) {
            return;
        }

        return $retail;
    }


}
