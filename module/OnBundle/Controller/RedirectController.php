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

namespace OnBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * RedirectController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RedirectController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        if (!($slug = $this->getSlug())) {
            return $this->notFoundAction();
        }

        $slug->incrementHits();
        $this->getDocumentManager()->flush();

        $this->redirect()->toUrl(
            $slug->getUrl()
        );

        return new ViewModel();
    }

    /**
     * @return \OnBundle\Document\Slug|null
     */
    private function getSlug()
    {
        return $this->getDocumentManager()
            ->getRepository('OnBundle\Document\Slug')
            ->findOneByName(strtolower($this->getParam('name', '')));
    }
}
