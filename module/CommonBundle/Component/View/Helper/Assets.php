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

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\EncoreTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;

/**
 * View helper that allows us to render Encore assets.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Assets extends \Zend\View\Helper\AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use EncoreTrait;

    /**
     * @param string $entryPoint
     * @return string
     */
    public function __invoke($entryPoint)
    {
        $assets = $this->getEncore()->getAssets($entryPoint);

        $renderedAssets = array();
        foreach ($assets as $type => $files) {
            foreach($files as $file) {
                $renderedAssets[] = $this->renderAsset($type, $file);
            }
        }

        return implode(PHP_EOL, $renderedAssets);
    }

    private function renderAsset($type, $file)
    {
        switch ($type) {
            case 'css':
                return sprintf(
                    '<link href="%s" media="screen" rel="stylesheet" type="text/css"/>',
                    $file
                );
                break;

            case 'js':
                return sprintf(
                    '<script src="%s" type="text/javascript"></script>',
                    $file
                );
                break;

            default:
                return null;
        }
    }
}
