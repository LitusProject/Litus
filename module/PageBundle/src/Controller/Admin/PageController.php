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
 
namespace PageBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PageBundle\Entity\Nodes\Page,
    PageBundle\Entity\Nodes\Translation,
    PageBundle\Form\Admin\Page\Add as AddForm,
    PageBundle\Form\Admin\Page\Edit as EditForm;

/**
 * PageController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PageController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Nodes\Page')
                ->findAll(),
            $this->getParam('page')
        );
        
        return array(
            'paginator' => $paginator,
            'paginationControl' => $this->paginator()->createControl(),
        );
    }
    
    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $page = new Page($this->getAuthentication()->getPersonObject());
                $this->getEntityManager()->persist($page);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();
                
                foreach($languages as $language) {
                    $translation = new Translation($page, $language, $formData['content_' . $language->getAbbrev()], $formData['title_' . $language->getAbbrev()]);
                    $this->getEntityManager()->persist($translation);
                    
                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();
                
                \CommonBundle\Component\Log\Log::createLog(
                    $this->getEntityManager(),
                    'action',
                    $this->getAuthentication()->getPersonObject(),
                    'Page added: ' . $title
                );
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The page was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page',
                    array(
                        'action' => 'manage'
                    )
                );
                
                return;
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function editAction()
    {
        if (!($page = $this->_getPage()))
            return;
        
        $form = new EditForm($this->getEntityManager(), $page);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $page->setUpdatePerson($this->getAuthentication()->getPersonObject());

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();
                
                foreach($languages as $language) {
                    $translation = $page->getTranslation($language);
                    
                    if ($translation) {
                        $translation->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        $translation = new Translation($page, $language, $formData['content_' . $language->getAbbrev()], $formData['title_' . $language->getAbbrev()]);
                        $this->getEntityManager()->persist($translation);
                    }
                    
                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();
                
                \CommonBundle\Component\Log\Log::createLog(
                    $this->getEntityManager(),
                    'action',
                    $this->getAuthentication()->getPersonObject(),
                    'Page edited: ' . $title
                );
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The page was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page',
                    array(
                        'action' => 'manage'
                    )
                );
                
                return;
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();

        if (!($page = $this->_getPage()))
            return;
        
        $this->getEntityManager()->remove($page);
        
        $this->getEntityManager()->flush();
        
        \CommonBundle\Component\Log\Log::createLog(
            $this->getEntityManager(),
            'action',
            $this->getAuthentication()->getPersonObject(),
            'Page deleted: ' . $page->getTitle(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en')
                )
        );
        
        return array(
            'result' => array(
                'status' => 'success'
            ),
        );
    }
    
    public function _getPage()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the page!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_page',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
    
        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findOneById($this->getParam('id'));
        
        if (null === $page) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No page with the given id was found!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_page',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
        
        return $page;
    }
}
