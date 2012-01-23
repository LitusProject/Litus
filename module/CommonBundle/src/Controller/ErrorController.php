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

namespace CommonBundle\Controller;

use Zend\Mvc\Controller\ActionController;

class ErrorController extends ActionController
{
    const ERROR_NO_ROUTE = 404;
    const ERROR_NO_CONTROLLER = 404;

    public function indexAction()
    {
        $error = $this->request->getMetadata('error', false);
        if (!$error) {
            $error = array(
                'type' => 404,
                'message' => 'Page Not Found'
            );
        }        
        switch ($error['type']) {
            case self::ERROR_NO_ROUTE:
            case self::ERROR_NO_CONTROLLER:
            default:
                $this->response->setStatusCode(404);
                break;
        }
        
        return array(
        	'message' => $error['message']
        );
    }
}
