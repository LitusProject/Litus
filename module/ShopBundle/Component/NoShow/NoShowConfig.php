<?php

namespace ShopBundle\Component\NoShow;

use CommonBundle\Entity\User\Person;

/**
 * This class is used to interpret the configuration value that defines the configuration of the no-show system.
 * The no-show system is used to notify users and create bans when users reserve items and don't pick them up.
 */
class NoShowConfig extends \CommonBundle\Component\Controller\ActionController\AdminController
{

    /**
     * @var array Stores the email related to each warning
     */
    private array $emailDictionary;

    /**
     * @var array Stores the amount of ban days related to each warning
     *
     * Current set-up of ban intervals: 0 days, 1 week, 1 week, 2 weeks, 3 weeks, 4 weeks, ...
     * In the config, 'default_element' is the last element and is at index 3
     */
    private array $banDaysDictionary;

    public function __construct($configData)
    {
        $this->emailDictionary = array();
        $this->banDaysDictionary = array();

        foreach ($configData as $index => $data) {
            if ($index === 'default_case') {
                $this->emailDictionary['default_case']['subject'] = $data['mail_subject'];
                $this->emailDictionary['default_case']['content'] = $data['mail_content'];
                $this->banDaysDictionary['default_case'] = $data['ban_days'];
            } else {
                $index = (int)$index;
                $this->emailDictionary[$index]['subject'] = $data['mail_subject'];
                $this->emailDictionary[$index]['content'] = $data['mail_content'];
                $this->banDaysDictionary[$index] = $data['ban_days'];
            }
        }
    }

    /**
     * Returns the ban interval for a certain warning count.
     *
     * @param integer $warningCount
     * @return mixed
     */
    public function getBanInterval(int $warningCount)
    {
        $default_index = array_search('default_case', array_keys($this->banDaysDictionary), true);
        if ($warningCount >= $default_index) {
            return 2 + $warningCount - $default_index . ' weeks';
        }
        return $this->banDaysDictionary[$warningCount];
    }

    /**
     * Returns the warning email content for the warningCount.
     *
     * @param Person  $person
     * @param integer $warningCount The amount of warnings the person already has.
     * @return array|string|string[]
     */
    public function getEmailContent(Person $person, int $warningCount, string $banInterval)
    {
        if ($warningCount >= count($this->banDaysDictionary) - 1) {
            $warningCount = 'default_case';
        }

        $mailContent = $this->emailDictionary[$warningCount]['content'];

        $name = $person->getFirstName();
        $mailContent = str_replace('{{ name }}', $name, $mailContent);

        $ban_interval_nl = $this->translateInterval($banInterval);
        $mailContent = str_replace('{{ ban_interval_nl }}', $ban_interval_nl, $mailContent);
        $mailContent = str_replace('{{ ban_interval_en }}', $banInterval, $mailContent);

        return $mailContent;
    }

    /**
     * Returns the warning email subject for the warningCount.
     *
     * @param integer $warningCount
     * @return mixed
     */
    public function getEmailSubject(int $warningCount)
    {
        if ($warningCount >= count($this->banDaysDictionary) - 1) {
            $warningCount = 'default_case';
        }

        return $this->emailDictionary[$warningCount]['subject'];
    }

    private function translateInterval(string $interval)
    {
        $interval = str_replace('day', 'dag', $interval);
        $interval = str_replace('days', 'dagen', $interval);
        $interval = str_replace('week', 'week', $interval);
        $interval = str_replace('weeks', 'weken', $interval);

        return $interval;
    }
}
