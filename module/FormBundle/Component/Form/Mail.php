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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Component\Form;

use CommonBundle\Entity\General\Language;
use FormBundle\Entity\Node\Entry as FormEntry;
use FormBundle\Entity\Node\Form as FormEntity;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface as MailTransport;
use Zend\Mvc\Controller\Plugin\Url;

/**
 * Send form mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail
{
    public static function send(FormEntry $formEntry, FormEntity $formSpecification, Language $language, MailTransport $mailTransport, Url $url, Request $request)
    {
        $urlString = ($request->getServer('HTTPS', 'off') === 'on' ? 'https://' : 'http://') . $request->getServer('HTTP_HOST') . $url->fromRoute(
            'form_view',
            array(
                'action' => 'login',
                'id'     => $formSpecification->getId(),
                'key'    => $formEntry->getGuestInfo() ? $formEntry->getGuestInfo()->getSessionId() : '',
            )
        );
        $mailAddress = $formSpecification->getMail()->getFrom();

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($formSpecification->getCompletedMailBody($formEntry, $language, $urlString))
            ->setFrom($mailAddress)
            ->setSubject($formSpecification->getMail()->getSubject())
            ->addTo($formEntry->getPersonInfo()->getEmail(), $formEntry->getPersonInfo()->getFullName());

        if ($formSpecification->getMail()->getBcc()) {
            $mail->addBcc($mailAddress);
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $mailTransport->send($mail);
        }
    }
}
