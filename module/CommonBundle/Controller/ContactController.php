<?php

namespace CommonBundle\Controller;

use Laminas\View\Model\ViewModel;

/**
 * ContactController
 *
 * At this moment this controller is not being used but, the code is still here so it can be used in the future.
 */
class ContactController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {

        return new ViewModel(
        );
    }
}
