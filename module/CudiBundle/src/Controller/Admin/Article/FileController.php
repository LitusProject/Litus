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
 
namespace CudiBundle\Controller\Admin\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Files\File,
	CudiBundle\Form\Admin\Article\File\Add as AddForm,
	CudiBundle\Form\Admin\Article\File\Edit as EditForm,
	Zend\File\Transfer\Adapter\Http as FileUpload,
	Zend\Http\Headers;

/**
 * FileController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FileController extends \CudiBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
		if (!($article = $this->_getArticle()))
		    return;
		
		$mappings = $this->getEntityManager()
		    ->getRepository('CudiBundle\Entity\Files\Mapping')
		    ->findAllByArticle($article);
		
		$form = new AddForm();
		$form->setAction(
		    $this->url()->fromRoute(
		        'admin_file',
		        array(
		            'action' => 'upload',
		            'id' => $article->getId(),
		        )
		    )
		);
        
        return array(
        	'form' => $form,
        	'article' => $article,
        	'mappings' => $mappings,
        	'uploadProgressName' => ini_get('session.upload_progress.name'),
        	'uploadProgressId' => uniqid(),
        );
	}
	
	public function uploadAction()
	{
	    $this->initAjax();
	    
	    if (!($article = $this->_getArticle()))
	        return;
	    
		$form = new AddForm();
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
    		
    		$file = new File(
    		    $this->getEntityManager(),
    		    $fileName,
    		    $originalName,
    		    $formData['description'],
    		    $article,
    		    $formData['printable']
    		);
    		$this->getEntityManager()->persist($file);
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
	
	public function editAction()
	{
	    if (!($mapping = $this->_getFileMapping()))
	        return;
	        
	    $form = new EditForm($mapping);
	    
	    if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
        	if ($form->isValid($formData)) {
        	    $mapping->setPrintable($formData['printable'])
        	        ->getFile()->setDescription($formData['description']);
        	    
        	    $this->getEntityManager()->flush();
        	    
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The file was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_file',
                	array(
                		'action' => 'manage',
                		'id' => $mapping->getArticle()->getId(),
                	)
                );
                
                return;
        	}
        }
	            
	    return array(
	        'form' => $form,
	        'file' => $mapping->getFile(),
	        'article' => $mapping->getArticle(),
	    );
	}
	
	public function deleteAction()
	{
		$this->initAjax();
					
		if (!($mapping = $this->_getFileMapping()))
		    return;

		$this->getEntityManager()->remove($mapping);
		$this->getEntityManager()->flush();
		
		return array(
		    'result' => (object) array('status' => 'success'),
		);
	}
	
	public function downloadAction()
	{
		$filePath = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.file_path');
			
		if (!($mapping = $this->_getFileMapping()))
		    return;
		
		$file = $mapping->getFile();
		
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
	
	public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->post()->get('upload_id');

        return array(
            'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
        );
    }
    
    private function _getArticle()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the article!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $article) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No article with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $article;
    }
    
    private function _getFileMapping()
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
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $file = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Files\Mapping')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $file) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No file with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_article',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $file;
    }
}