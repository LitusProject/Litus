<?php

namespace Litus\Repository\Config;

use Doctrine\ORM\EntityRepository;

/**
 * The repository for configuration entries.
 * 
 */
class Config extends EntityRepository
{

    /**
     * @param string $key
     * @return string
     */
    public function getConfigValue($key)
    {
        /** @var $config \Litus\Entity\Config\Config */
        $config = $this->find($key);
        if($config === null)
            throw new \RuntimeException('Configuration entry ' . $key . ' not found.');
        return $config->getValue();
    }
}