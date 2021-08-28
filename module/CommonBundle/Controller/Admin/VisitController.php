<?php

namespace CommonBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;

/**
 * VisitController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class VisitController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = null;
        if ($this->getParam('field') !== null) {
            $visits = $this->search();
            if ($visits === null) {
                return new ViewModel();
            }

            $paginator = $this->paginator()->createFromQuery(
                $visits,
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
            $paginator = $this->paginator()->createFromEntity(
                'CommonBundle\Entity\General\Visit',
                $this->getParam('page'),
                array(),
                array(
                    'timestamp' => 'DESC',
                )
            );
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $visits = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($visits as $visit) {
            $item = (object) array();
            $item->id = $visit->getId();
            $item->timestamp = $visit->getTimestamp()->format('d/m/Y H:i:s');
            $item->browser = $visit->getBrowser();
            $item->url = $visit->getUrl();
            $item->requestMethod = $visit->getRequestMethod();
            $item->controller = $visit->getController();
            $item->action = $visit->getAction();
            $item->user = $visit->getUser() ? $visit->getUser()->getFullName() : '';

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'controller':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Visit')
                    ->findAllByControllerQuery($this->getParam('string'));
            case 'user':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Visit')
                    ->findAllByUserQuery($this->getParam('string'));
            case 'url':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Visit')
                    ->findAllByUrlQuery($this->getParam('string'));
        }
    }
}
