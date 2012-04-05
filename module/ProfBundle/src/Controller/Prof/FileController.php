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
 
namespace ProfBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\File,
    ProfBundle\Entity\Action\File\Add as AddAction,
    ProfBundle\Entity\Action\File\Remove as RemoveAction,
    ProfBundle\Form\Prof\File\Add as FileForm,
    Doctrine\ORM\EntityManager,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers;

/**
 * FileController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FileController extends \ProfBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return;
            
        $allFiles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\File')
            ->findAllByArticle($article, false);
        
        $files = array();
        foreach($allFiles as $file) {
            $removeAction = $this->getEntityManager()
                ->getRepository('ProfBundle\Entity\Action\File\Remove')
                ->findOneByFile($file);
            if (null === $removeAction)
                $files[] = $file;
        }
        
        $form = new FileForm();
        $form->setAction(
            $this->url()->fromRoute(
                'prof_file',
                array(
                    'action' => 'upload',
                    'id' => $article->getId(),
                )
            )
        );
            
    	return array(
    	    'form' => $form,
    	    'article' => $article,
    	    'articleFiles' => $files,
    	    'uploadProgressName' => ini_get('session.upload_progress.name'),
    	    'uploadProgressId' => uniqid(),
    	);
    }
    
    public function downloadAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		if (!($file = $this->_getFile()))
		    return;
		
		$headers = new Headers();
		$headers->addHeaders(array(
			'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
			'Content-type' => 'application/octet-stream',
			'Content-Length' => filesize($filePath . $file->getPath()),
		));
		$this->getResponse()->setHeaders($headers);

		$handle = fopen($filePath . $file->getPath(), 'r');
		$data = fread($handle, filesize($filePath . $file->getPath()));
		fclose($handle);
		
		return array(
			'data' => $data
		);
	}
	
	public function uploadAction()
	{
	    $this->initAjax();
	    
	    if (!($article = $this->_getArticle()))
	        return;
	    
		$form = new FileForm();
	    $formData = $this->getRequest()->post()->toArray();
	            	
    	if ($form->isValid($formData)) {
    	    $filePath = $this->getEntityManager()
    	    	->getRepository('CommonBundle\Entity\General\Config')
    	    	->getConfigValue('cudi.file_path');
    	    	
    		$upload = new FileUpload();
    		$originalName = $upload->getFileName(null, false);

    		$fileName = '';
    		do{
    		    $fileName = '/' . sha1(uniqid());
    		} while (file_exists($filePath . $fileName));
    		
    		$upload->addFilter('Rename', $filePath . $fileName);
    		$upload->receive();
    		
    		$file = new File($fileName, $originalName, $formData['description'], $article);
    		$file->setEnabled(false);
    		$this->getEntityManager()->persist($file);
    		
    		$action = new AddAction($this->getAuthentication()->getPersonObject(), $file);
    		$this->getEntityManager()->persist($action);
    		
    		$this->getEntityManager()->flush();
    		
    		return array(
    		    'status' => 'success',
    		    'info' => array(
    		        'info' => (object) array(
    		            'name' => $file->getName(),
    		            'description' => $file->getDescription(),
    		            'id' => $file->getId(),
    		        )
    		    ),
    		);
    	} else {
    	    $errors = $form->getErrors();
    	    $formErrors = array();
    	    
    	    foreach ($form->getElements() as $key => $element) {
    	        $formErrors[$element->getId()] = array();
    	        foreach ($errors[$element->getName()] as $error) {
    	            $formErrors[$element->getId()][] = $element->getMessages()[$error];
    	        }
    	    }
    	    
    	    return array(
    	        'status' => 'error',
    	        'form' => array(
    	            'errors' => $formErrors
    	        ),
    	    );
    	}
	}
	
	public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->post()->get('upload_id');

        return array(
            'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($file = $this->_getFile()))
            return;
            
        if ($file->isEnabled()) {
            $action = new RemoveAction($this->getAuthentication()->getPersonObject(), $file);
            $this->getEntityManager()->persist($action);
        } else {
            $filePath = $this->getEntityManager()
            	->getRepository('CommonBundle\Entity\General\Config')
            	->getConfigValue('cudi.file_path');
            
            $action = $this->getEntityManager()
                ->getRepository('ProfBundle\Entity\Action\File\Add')
                ->findOneByFile($file);
            $this->getEntityManager()->remove($action);
            $this->getEntityManager()->remove($file);
            
            unlink($filePath . $file->getPath());
            $this->getEntityManager()->remove($file);
        }
        
        $this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array('status' => 'success'),
        );
    }
    
    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;
        
    	if (null === $id) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($id);
    	
    	$subjects = $this->getEntityManager()
    	    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
    	    ->findAllByProf($this->getAuthentication()->getPersonObject());
    	
    	foreach($subjects as $subject) {
    	    $mapping = $this->getEntityManager()
    	        ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
    	        ->findOneByArticleAndSubject($article, $subject->getSubject());
    	    
    	    if ($mapping)
    	        break;
    	}
    	
    	if (null === $article || null === $mapping) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
    
    private function _getFile()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the file!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $file = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\File')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $file || null == $this->_getArticle($file->getInternalArticle()->getId())) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No file with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'prof_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $file;
    }
}