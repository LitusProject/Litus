<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile,
    MailBundle\Component\Generator\MailingList\Zip,
    MailBundle\Entity\Users\People\Academic,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * MailController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getAllListsAction()
    {
        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findAll();

        $archive = new TmpFile();
        $generator = new Zip($this->getEntityManager(), $lists);
        $generator->generateArchive($archive);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="lists.zip"',
            'Content-Type' => mime_content_type($archive->getFileName()),
            'Content-Length' => filesize($archive->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        var_dump($archive->getFileName());

        $handle = fopen($archive->getFileName(), 'r');
        $data = fread($handle, filesize($archive->getFileName()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }
}
