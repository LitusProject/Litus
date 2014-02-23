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

namespace SportBundle\Command;

use DateTime;

/**
 * Cache the JSON of the official result page.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class CacheResult extends \CommonBundle\Component\Console\Command
{
    public function configure()
    {
        $this
            ->setName('sport:cache-result')
            ->setDescription('fetch and store the results of the competition')
            ->setHelp(<<<EOT
The %command.name% command fetches the results of the competition and stores
them in a cache.
EOT
        );
    }

    protected function executeCommand()
    {
        while (true) {
            $resultPage = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_result_page');

            $options = array(
                'http' => array(
                    'timeout' => 0.5,
                )
            );

            $fileContents = @file_get_contents($resultPage, false, stream_context_create($options));
            $resultPage = json_decode($fileContents);

            if (false !== $fileContents && null !== $resultPage) {
                file_put_contents('data/cache/' . md5('run_result_page'), $fileContents);
                $this->writeln('Succesfully cached the result page');

                sleep(substr($resultPage->update, 0, strlen($resultPage->update)-1));
            } else {
                $this->writeln('Failed to cache the result page');
                sleep(10);
            }
        }
    }

    protected function getLogName()
    {
        return 'CacheSportResult';
    }

    public function write($str, $raw = false)
    {
        $now = new DateTime();
        return parent::write(
            sprintf('[<%1$s>%2$s</%1$s>] %3$s', $this->getLogNameTag(), $now->format('Ymd H:i:s'), $str),
            $raw
        );
    }

    public function writeln($str, $raw = false)
    {
        $now = new DateTime();
        return parent::writeln(
            sprintf('[<%1$s>%2$s</%1$s>] %3$s', $this->getLogNameTag(), $now->format('Ymd H:i:s'), $str),
            $raw
        );
    }
}
