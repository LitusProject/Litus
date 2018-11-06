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
    protected function configure()
    {
        $this
            ->setName('sport:cache-result')
            ->setDescription('Fetch and store the competition results')
            ->setHelp(
                <<<EOT
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
                ),
            );

            $fileContents = @file_get_contents($resultPage, false, stream_context_create($options));
            $resultPage = json_decode($fileContents);

            if ($fileContents !== false && $resultPage !== null) {
                file_put_contents('data/cache/run-' . md5('run_result_page'), $fileContents);
                $this->writeln('Succesfully cached the result page');

                $delay = (int) $resultPage->update;
                sleep($delay ? $delay : 3);
            } else {
                $this->writeln('Failed to cache the result page');
                sleep(3);
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

        parent::write(
            sprintf('[<%1$s>%2$s</%1$s>] %3$s', $this->getLogNameTag(), $now->format('Ymd H:i:s'), $str),
            $raw
        );
    }

    public function writeln($str, $raw = false)
    {
        $now = new DateTime();

        parent::writeln(
            sprintf('[<%1$s>%2$s</%1$s>] %3$s', $this->getLogNameTag(), $now->format('Ymd H:i:s'), $str),
            $raw
        );
    }
}
