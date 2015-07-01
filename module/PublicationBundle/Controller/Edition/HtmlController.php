<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PublicationBundle\Controller\Edition;

use PublicationBundle\Entity\Edition\Html as HtmlEdition,
    Zend\View\Model\ViewModel;

/**
 * HtmlController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($edition = $this->getHtmlEditionEntity())) {
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
