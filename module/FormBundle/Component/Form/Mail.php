<?php

namespace FormBundle\Component\Form;

use CommonBundle\Entity\General\Language;
use FormBundle\Entity\Node\Entry as FormEntry;
use FormBundle\Entity\Node\Form as FormEntity;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface as MailTransport;
use Laminas\Mvc\Controller\Plugin\Url;

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
