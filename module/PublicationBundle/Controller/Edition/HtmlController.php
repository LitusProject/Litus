<?php

namespace PublicationBundle\Controller\Edition;

use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Edition\Html as HtmlEdition;

/**
 * HtmlController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $edition = $this->getHtmlEditionEntity();
        if ($edition === null) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'edition' => $edition,
            )
        );
    }

    /**
     * @return HtmlEdition|null
     */
    private function getHtmlEditionEntity()
    {
        $edition = $this->getEntityById('PublicationBundle\Entity\Edition\Html');

        if (!($edition instanceof HtmlEdition)) {
            $this->flashMessenger()->error(
                'Error',
                'No edition was found!'
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $edition;
    }
}
