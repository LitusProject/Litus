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

namespace CommonBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

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
