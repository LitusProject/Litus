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
     */
    public function getEmailContent(Person $person, int $warningCount) {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }

        $mailContent = $this->emailDictionary[$warningCount]['content'];

        $name = $person->getFirstName();
        $mailContent = str_replace('{{ name }}', $name, $mailContent);

        return $mailContent;
    }

    public function getEmailSubject(int $warningCount) {
        if ($warningCount >= count($this->banDaysDictionary)) {
            $warningCount = count($this->banDaysDictionary) - 1;
        }

        return $this->emailDictionary[$warningCount]['subject'];
    }
}