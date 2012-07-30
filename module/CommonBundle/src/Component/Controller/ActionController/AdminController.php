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
 
namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Entity\General\Language,
    CommonBundle\Form\Auth\Login as LoginForm,
	Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AdminController extends \CommonBundle\Component\Controller\ActionController
{
	/**
     * Execute the request.
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function execute(MvcEvent $e)
    {
		$result = parent::execute($e);
		
		$language = $this->getEntityManager()
		    ->getRepository('CommonBundle\Entity\General\Language')
		    ->findOneByAbbrev('en');
		    
		if (null === $language) {
		    $language = new Language(
		        'en', 'English'
		    );
		}
		    
		$result->language = $language;
		$result->now = array(
			'iso8601' => date('c', time()),
			'display' => date('l, F j Y, H:i', time())
		);
  		
        $e->setResult($result);
        
        return $result;
    }
    
    /**
     * Initializes the localization
     *
     * @return void
     */
    protected function initLocalization()
    {
        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');
            
        if (null === $language) {
            $language = new Language(
                'en', 'English'
            );
        }

        $this->getLocator()->get('translator')->setLocale(
            $language->getAbbrev()
        );

        \Zend\Registry::set('Zend_Locale', $language->getAbbrev());
        \Zend\Registry::set('Zend_Translator', $this->getLocator()->get('translator'));
        
        if ($this->getAuthentication()->isAuthenticated()) {
        	$this->getAuthentication()->getPersonObject()->setLanguage($language);
        	$this->getEntityManager()->flush();
        }
    }
}
