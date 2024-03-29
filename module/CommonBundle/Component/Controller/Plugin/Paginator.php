<?php

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrinePaginatorAdapter;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Exception;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator as LaminasPaginator;

/**
 * A controller plugin containing some utility methods for pagination.
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */
class Paginator extends \Laminas\Mvc\Controller\Plugin\AbstractPlugin implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use DoctrineTrait;

    /**
     * @var LaminasPaginator $paginator The paginator
     */
    private $paginator = null;

    /**
     * @var integer The number of items on each page
     */
    private $itemsPerPage = 25;

    /**
     * Setting the number of items per page, defaults to 25.
     *
     * @param  integer $itemsPerPage The number of items per page
     * @return void
     */
    public function setItemsPerPage($itemsPerPage)
    {
        if (!is_int($itemsPerPage) || $itemsPerPage < 0) {
            throw new Exception\InvalidArgumentException('The number of items per page has to be positive integer');
        }

        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Get the number of items per page.
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Create a paginator from a given array.
     *
     * @param  array   $records     The array containing the paginated records
     * @param  integer $currentPage The page we now are on
     * @return LaminasPaginator
     */
    public function createFromArray(array $records, $currentPage)
    {
        $this->paginator = new LaminasPaginator(
            new ArrayAdapter($records)
        );

        $this->paginator->setCurrentPageNumber($currentPage);
        $this->paginator->setItemCountPerPage(
            $this->itemsPerPage
        );

        return $this->paginator;
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param  string  $entity      The name of the entity that should be paginated
     * @param  integer $currentPage The page we now are on
     * @param  array   $conditions  These conditions will be passed to the Repository call
     * @param  array   $orderBy     An array containing constraints on how to order the results
     * @return LaminasPaginator
     */
    public function createFromEntity($entity, $currentPage, array $conditions = array(), array $orderBy = array())
    {
        $qb = $this->getEntityManager()
            ->getRepository($entity)
            ->createQueryBuilder('e');
        foreach (array_keys($conditions) as $fieldName) {
            $qb->andWhere('e.' . $fieldName . ' = :' . $fieldName);
        }
        foreach ($orderBy as $fieldName => $orientation) {
            $qb->addOrderBy('e.' . $fieldName, $orientation);
        }
        $qb->setParameters($conditions);

        return $this->createFromQuery($qb, $currentPage);
    }

    /**
     * Create a paginator for the given Doctrine ORM query
     *
     * @param  Query|QueryBuilder $query       The query that should be paginated
     * @param  integer            $currentPage The page we now are on
     * @return LaminasPaginator
     */
    public function createFromQuery($query, $currentPage)
    {
        $this->paginator = new LaminasPaginator(
            new DoctrinePaginatorAdapter(new DoctrinePaginator($query))
        );

        $this->paginator->setCurrentPageNumber($currentPage);
        $this->paginator->setItemCountPerPage($this->itemsPerPage);

        return $this->paginator;
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param  integer $currentPage The page we now are on
     * @return LaminasPaginator
     */
    public function createFromPaginatorRepository(array $records, $currentPage, $totalNumber)
    {
        $currentPage = $currentPage == 0 ? $currentPage = 1 : $currentPage;

        $prefix = array();
        if ($currentPage > 1) {
            $prefix = array_fill(0, $this->itemsPerPage * ($currentPage - 1), true);
        }

        $suffix = array();
        if ($totalNumber - ($this->itemsPerPage * $currentPage) > 0) {
            $suffix = array_fill(0, $totalNumber - ($this->itemsPerPage * $currentPage), true);
        }

        $data = array_merge(
            $prefix,
            $records,
            $suffix
        );

        $this->paginator = new LaminasPaginator(
            new ArrayAdapter($data)
        );

        $this->paginator->setCurrentPageNumber($currentPage);
        $this->paginator->setItemCountPerPage(
            $this->itemsPerPage
        );

        return $this->paginator;
    }

    /**
     * A method to quickly create the array needed to build the pagination control.
     *
     * @param  boolean $fullWidth Whether the paginationControl should be displayed using the full width or not
     * @return array|null
     */
    public function createControl($fullWidth = false)
    {
        $controller = $this->getController();
        if (!($controller instanceof AbstractController)) {
            return;
        }

        $params = $controller->getEvent()->getRouteMatch()->getParams();
        foreach ($params as $key => $param) {
            if ($param === '') {
                unset($params[$key]);
            }

            if (isset($params['page'])) {
                unset($params['page']);
            }
        }

        if ($controller->getRequest() instanceof HttpRequest) {
            $query = $controller->getRequest()->getQuery();
        } else {
            $query = array();
        }

        return array(
            'fullWidth'          => $fullWidth,
            'matchedRouteName'   => $controller->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'matchedRouteParams' => $params,
            'query'              => count($query) > 0 ? '?' . $query->toString() : '',
            'pages'              => $this->paginator->getPages(),
        );
    }
}
