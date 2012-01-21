<?php

class ErrorController extends Zend\Controller\Action
{
    public function errorAction()
    {
        $this->broker('layout')->disableLayout();

        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_ROUTE:
            case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->vars()->message = 'Page Not Found';
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->vars()->message = 'Application Error';
                break;
        }
        
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->vars()->exception = $errors->exception;
        }

        $this->view->vars()->request = $errors->request;
    }
}

