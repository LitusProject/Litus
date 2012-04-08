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
 
namespace ProfBundle\Component\Controller;

use CommonBundle\Component\Controller\Exception\HasNoAccessException,
    CommonBundle\Form\Auth\Login as LoginForm,
    CudiBundle\Entity\Article,
	Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController
{
	/**
     * Execute the request
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function execute(MvcEvent $e)
    {
		$result = parent::execute($e);
				
		$result['authenticatedUserObject'] = $this->getAuthentication()->getPersonObject();
		$result['authenticated'] = $this->getAuthentication()->isAuthenticated();
		$result['loginForm'] = new LoginForm($this->url()->fromRoute('prof_auth', array('action' => 'login')));
		
		$result['unionUrl'] = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.union_url');
  		
        $e->setResult($result);
        return $result;
    }
    
    protected function applyEditsArticle(Article $article)
    {
        $editItems = $this->getEntityManager()
            ->getRepository('ProfBundle\Entity\Action\Article\Edit\Item')
            ->findAllByArticle($article);
            
        foreach($editItems as $item) {
            if ($item->getField() == 'title')
                $article->setTitle($item->getValue());
            if ($item->getField() == 'author')
                $article->getMetaInfo()->setAuthors($item->getValue());
            if ($item->getField() == 'publisher')
                $article->getMetaInfo()->setPublishers($item->getValue());
            if ($item->getField() == 'year_published')
                $article->getMetaInfo()->setYearPublished($item->getValue());
            if ($item->getField() == 'binding')
                $article->setBinding($this->getEntityManager()
                	->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
                	->findOneById($item->getValue()));
            if ($item->getField() == 'rectoverso')
                $article->setIsRectoVerso($item->getValue());
        }
    }
}
