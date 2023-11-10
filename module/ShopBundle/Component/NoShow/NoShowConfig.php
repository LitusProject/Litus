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

        foreach ($configData as $index => $data) {
            $index = (int)$index; // This conversion may not be necessary

            $this->emailDictionary[$index]['subject'] = $data['mail_subject'];
            $this->emailDictionary[$index]['content'] = $data['mail_content'];
            $this->banDaysDictionary[$index] = $data['ban_days'];
        }
        error_log(json_encode($this->banDaysDictionary));
    }

    /**
     * Returns the amount of days of ban for the warningCount.
     *
     * @param int $warningCount
     * @return mixed
     */
    public function getBanInterval(int $warningCount) {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }
        return $this->banDaysDictionary[$warningCount];
    }

    /**
     * Returns the warning email when a no-show is assigned to $person.
     *
     * @param Person $person
     * @param int $warningCount
     * @return Message
     */
    public function getEmail(Person $person, int $warningCount) {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }

        $mailSubject = $this->emailDictionary[$warningCount]['subject'];
        $mailContent = $this->emailDictionary[$warningCount]['content'];

        $name = $person->getFirstName();
        $mailContent = str_replace('{{ name }}', $name, $mailContent);

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
