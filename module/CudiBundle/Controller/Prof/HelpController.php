<?php

namespace CudiBundle\Controller\Prof;

use Laminas\View\Model\ViewModel;

/**
 * HelpController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HelpController extends \CudiBundle\Component\Controller\ProfController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
