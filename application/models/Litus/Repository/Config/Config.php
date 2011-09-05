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

    /**
     * @param string $prefix the prefix, example: 'litus.application'. No ending '.'!
     * @return array
     */
    public function getAllByPrefix($prefix)
    {
        $configs = $this->_em->createQuery('SELECT c FROM Litus\Entity\Config\Config c WHERE c.key LIKE \'' . $prefix . '.%\'')
                        ->getResult();

        $result = array();
        foreach($configs as $config) {
            $key = $config->getKey();
            $value = $config->getValue();

            $key = str_replace($prefix . '.','', $key);

            $result[$key] = $value;
        }

        return $result;
    }
}