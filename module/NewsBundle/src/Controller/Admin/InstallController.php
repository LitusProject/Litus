<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
  
namespace NewsBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->_installLanguages();                
    }
    
    protected function initAcl()
    {
        $this->installAcl(
            array(
                'newsBundle' => array(
                    'admin_news' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                ),
            )
        );
    }
    
    private function _installLanguages()
    {
        $languages = array(
            'en' => 'English',
            'nl' => 'Dutch'
        );
        
        foreach($languages as $abbrev => $name) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($abbrev);
                
            if (null == $language) {
                $language = new Language($abbrev, $name);
                $this->getEntityManager()->persist($language);
            }
        }
        
        $this->getEntityManager()->flush();
    }
}
