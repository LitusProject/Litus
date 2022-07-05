<?php

namespace ApiBundle\Controller;

use BrBundle\Entity\Company;
use Laminas\View\Model\ViewModel;

/**
 * BrController
 */
class BrController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function newCompanyAction()
    {

    }

    /**
     * input: json {
     *      "key": "api key",
     *      "company": "company name"
     * }
     */
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
            ->getResult()[0];

        $company->addCvBookYear($this->getCurrentAcademicYear());

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }
}