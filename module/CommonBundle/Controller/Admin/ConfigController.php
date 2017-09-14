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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Config,
    Zend\View\Model\ViewModel;

/**
 * ConfigController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ConfigController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $configValues = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->findAll();

        $formattedValues = array();
        foreach ($configValues as $entry) {
            if (strstr($entry->getKey(), Config::$separator)) {
                $explodedKey = explode(Config::$separator, $entry->getKey());
                $formattedValues[$explodedKey[0]][$explodedKey[1]] = array(
                    'value' => $entry->getValue(),
                    'fullKey' => $entry->getKey(),
                );
            } else {
                $formattedValues[0][$entry->getKey()] = array(
                    'value' => $entry->getValue(),
                    'fullKey' => $entry->getKey(),
                );
            }
        }

        ksort($formattedValues, SORT_STRING);

        return new ViewModel(
            array(
                'configValues' => $formattedValues,
            )
        );
    }

    public function editAction()
    {
        if (!($entry = $this->getConfigEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('common_config_edit', array('config' => $entry));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The configuration entry was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'common_admin_config',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'entry' => $entry,
                'form' => $form,
            )
        );
    }

    /**
     * @return Config|null
     */
    private function getConfigEntity()
    {
        $config = $this->getEntityById('CommonBundle\Entity\General\Config', 'key', 'key');

        if (!($config instanceof Config)) {
            $this->flashMessenger()->error(
                'Error',
                'No config was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_config',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $config;
    }
}
