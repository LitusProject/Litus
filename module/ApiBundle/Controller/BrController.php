<?php

namespace ApiBundle\Controller;

use BrBundle\Entity\Company;

/**
 * BrController
 */
class BrController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function newCompanyAction()
    {

    }

    public function addCvBookAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $input = $this->getRequest()->getContent();
        $json = json_decode($input);

        $company_name = $json->company;

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findallByNameQuery($company_name)
            ->getResult();

    }


}