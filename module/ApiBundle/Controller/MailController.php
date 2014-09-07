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

namespace ApiBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile,
    MailBundle\Component\Archive\Generator\MailingList\Zip,
    MailBundle\Component\Archive\Generator\MailingList\Tar,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * MailController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function aliasesAction()
    {
        $aliases = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
            ->findAll();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => 'text/plain'
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'aliases' => $aliases
            )
        );
    }

    public function listsArchiveAction()
    {
        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findAll();

        if (0 == count($lists))
            throw new \RuntimeException('There needs to be at least one list before an archive can be created');

        $archive = new TmpFile();
        $generator = ('zip' != $this->getParam('type'))
            ? new Tar($this->getEntityManager(), $lists)
            : new Zip($this->getEntityManager(), $lists);
        $generator->generateArchive($archive);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="lists.' . ('zip' != $this->getParam('type') ? 'tar.gz' : 'zip') . '"',
            'Content-Type'        => mime_content_type($archive->getFileName()),
            'Content-Length'      => filesize($archive->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $archive->getContent()
            )
        );
    }
}
