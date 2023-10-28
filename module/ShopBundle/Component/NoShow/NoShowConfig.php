<?php

namespace ShopBundle\Component\NoShow;

use CommonBundle\Component\Controller\ActionController\AdminController;
use CommonBundle\Entity\User\Person;
use Laminas\Mail\Message;
use Nette\NotImplementedException;

class NoShowConfig extends \CommonBundle\Component\Controller\ActionController\AdminController {

    /**
     * @var array Stores the email related to each warning
     */
    private array $emailDictionary;

    /**
     * @var array Stores the amount of ban days related to each warning
     */
    private array $banDaysDictionary;

    public function __construct($configData) {
        $this->emailDictionary = [];
        $this->banDaysDictionary = [];

        foreach ($configData as $index) {
            $this->emailDictionary[$index]['subject'] = $configData[$index]['mail_subject'];
            $this->emailDictionary[$index]['content'] = $configData[$index]['mail_content'];
            $this->banDaysDictionary[$index] = $configData[$index]['ban_days'];
        }
    }

    public function getBanInterval(int $warningCount) {
        return $this->banDaysDictionary[$warningCount];
    }

    public function getEmail(Person $person, int $warningCount) {
        $mailSubject = $this->emailDictionary[$warningCount]['subject'];
        $mailContent = $this->emailDictionary[$warningCount]['content'];
        $name = $person->getFirstName();

        // sender address
        $noreplyAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.no-reply_mail');

        // bcc and reply address
        $shopAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.mail');

        // name of the shop
        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.no-reply_mail_name');

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($mailContent)
            ->setFrom($noreplyAddress, $mailName)
            ->setReplyTo($shopAddress, $mailName)
            ->addTo($person->getEmail(), $person->getFullName())
            ->setSubject($mailSubject)
            ->addBcc($shopAddress, $mailName);

        return $mail;
    }
}
