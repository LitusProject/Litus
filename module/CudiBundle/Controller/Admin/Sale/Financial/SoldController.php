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

namespace CudiBundle\Controller\Admin\Sale\Financial;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * SoldController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SoldController extends \CudiBundle\Component\Controller\ActionController
{
    public function individualAction()
    {
        if (null !== $this->getParam('field'))
            list($records, $totalNumber) = $this->_individualSearch($this->getParam('page'), $this->paginator()->getItemsPerPage());

        if (!isset($records)) {
            list($records, $totalNumber) = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findAllPaginator($this->getParam('page'), $this->paginator()->getItemsPerPage());
        }

        $paginator = $this->paginator()->createFromPaginatorRepository(
            $records,
            $this->getParam('page'),
            $totalNumber
        );

        foreach($paginator as $item) {
            $item->getSession()->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function individualSearchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        list($records, $totalNumber) = $this->_individualSearch(0, $numResults);

        $result = array();
        foreach($records as $soldItem) {
            $item = (object) array();
            $item->id = $soldItem->getId();
            $item->timestamp = $soldItem->getTimestamp()->format('d/m/Y H:i');
            $item->session = $soldItem->getSession()->getOpenDate()->format('d/m/Y H:i');
            $item->article = $soldItem->getArticle()->getMainArticle()->getTitle();
            $item->person = $soldItem->getPerson()->getFullName();
            $item->organization = $soldItem->getPerson()->getOrganization($soldItem->getSession()->getAcademicYear())->getName();
            $item->number = $soldItem->getNumber();
            $item->sellPrice = number_format($soldItem->getPrice()/100, 2);
            $item->purchasePrice = number_format($soldItem->getPurchasePrice()/100, 2);
            $item->discount = $soldItem->getDiscountType();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _individualSearch($page, $numberRecords)
    {
        switch($this->getParam('field')) {
            case 'article':
                return null;
            case 'person':
                return null;
            case 'organization':
                return null;
            case 'discount':
                return null;
        }
    }
}
