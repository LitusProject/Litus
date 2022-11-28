<?php

namespace CommonBundle\Controller;

use Laminas\View\Model\ViewModel;

/**
 * ContactController
 */
class ContactController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction() {

        $contactInfo = array(
            'activiteiten'      => 'activiteiten@vtk.be',
            'bedrijvenrelaties' => 'bedrijvenrelaties@vtk.be',
        );

        return new ViewModel(
            array(
                'contactInfo'  => $contactInfo
            )
        );
    }
}