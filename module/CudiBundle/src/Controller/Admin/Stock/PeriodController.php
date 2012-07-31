<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Controller\Admin\Stock;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Stock\PeriodValues\Start as StartValue,
    Zend\View\Model\ViewModel;

/**
 * PeriodController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PeriodController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Period',
            $this->getParam('page'),
            array(),
            array(
                'startDate' => 'DESC'
            )
        );
        
        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }
    
    public function newAction()
    {
        $previous = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        
        $new = new Period($this->getAuthentication()->getPersonObject());
        $this->getEntityManager()->persist($new);
        
        if ($previous) {
            $previous->setEntityManager($this->getEntityManager());
            $previous->close();
            
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($previous);
            foreach($articles as $article) {
                $value = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\PeriodValues\Start')
                        ->findValueByArticleAndPeriod($article, $previous)
                    + $previous->getNbDelivered($article)
                    - $previous->getNbSold($article);
    
                $start = new StartValue($article, $new, ($value < 0 ? 0 : $value));
                $this->getEntityManager()->persist($start);
            }
        }

        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The stock period was succesfully created.'
            )
        );
        
        $this->redirect()->toRoute(
            'admin_stock_period',
            array(
                'action' => 'manage'
            )
        );
        
        return new ViewModel();
    }
    
    public function viewAction()
    {
        if (!($period = $this->_getPeriod()))
            return new ViewModel();
            
        $period->setEntityManager($this->getEntityManager());
            
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period),
            $this->getParam('page')
        );
        
        return new ViewModel(
            array(
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
    
    public function searchAction()
    {
        if (!($period = $this->_getPeriod()))
            return new ViewModel();
            
        $period->setEntityManager($this->getEntityManager());
            
        switch($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndTitle($period, $this->getParam('string'));
                break;
            case 'barcode':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndBarcode($period, $this->getParam('string'));
                break;
            case 'supplier':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndSupplier($period, $this->getParam('string'));
                break;
        }
        
        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');
        
        array_splice($articles, $numResults);
        
        $result = array();
        foreach($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->delivered = $period->getNbDelivered($article);
            $item->ordered = $period->getNbOrdered($article);
            $item->sold = $period->getNbSold($article);
            $result[] = $item;
        }
        
        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
    
    private function _getPeriod()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the period!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_stock_period',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
    
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneById($this->getParam('id'));
        
        if (null === $period) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No period with the given id was found!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_stock_period',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
        
        return $period;
    }
}
