<?php

namespace SportBundle\Command;

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
        parent::configure();

        $this->setName('sport:cache-result')
            ->setDescription('Fetch and store the competition results');
    }

    protected function invoke()
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

            $this->write('Caching the result page...');
            if ($fileContents !== false && $resultPage !== null) {
                file_put_contents('data/cache/run-' . md5('run_result_page'), $fileContents);
                $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
            } else {
                $this->writeln(" <fg=red>\u{2717}</fg=red>", true);
                sleep(3);
            }
        }
    }
}
