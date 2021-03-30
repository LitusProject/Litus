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

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Article\External as ExternalArticle;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use InvalidArgumentException;

class Article extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $articleKeys = array(
        'title', 'year_published', 'isbn', 'url',
    );

    private static $internalKeys = array(
        'nb_black_and_white', 'nb_colored',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array(
            'article' => $this->stdExtract(
                $object,
                array(
                    self::$articleKeys,
                    array('downloadable', 'same_as_previous_year', 'internal'),
                )
            ),
        );

        $data['article']['authors'] = $object->getAuthors();
        $data['article']['publisher'] = $object->getPublishers();
        $data['article']['type'] = $object->getType();

        if (isset($data['article']['internal']) && $data['article']['internal']) {
            $data['internal'] = $this->stdExtract(
                $object,
                array(
                    self::$internalKeys,
                    array('perforated', 'hardcovered', 'official'),
                )
            );

            $data['internal']['colored'] = $object->isColored();
            $data['internal']['binding'] = $object->getBinding() ? $object->getBinding()->getId() : '';
            $data['internal']['front_color'] = $object->getFrontColor() ? $object->getFrontColor()->getId() : '';
            $data['internal']['rectoverso'] = $object->isRectoVerso();
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            if (!isset($data['article']['internal'])) {
                throw new InvalidArgumentException('Form data doesn\'t show whether to create an internal article or not');
            }

            if ($data['article']['internal']) {
                $object = new InternalArticle();
            } else {
                $object = new ExternalArticle();
            }
        }

        if (isset($data['article'])) {
            $this->stdHydrate($data['article'], $object, self::$articleKeys);

            $object->setAuthors($data['article']['authors'])
                ->setPublishers($data['article']['publisher'])
                ->setIsDownloadable($data['article']['downloadable'])
                ->setIsSameAsPreviousYear($data['article']['same_as_previous_year'])
                ->setType(isset($data['article']['type']) && $data['article']['type'] != '' ? $data['article']['type'] : 'common');
        }

        if ($object->isInternal() && isset($data['internal'])) {
            $binding = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                ->findOneById($data['internal']['binding']);

            $frontPageColor = null;
            if (isset($data['internal']['front_color'])) {
                $frontPageColor = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\Option\Color')
                    ->findOneById($data['internal']['front_color']);
            }

            $this->stdHydrate($data['internal'], $object, self::$internalKeys);

            $colored = ($data['internal']['colored'] || $data['internal']['nb_colored'] > 0);
            $object->setBinding($binding)
                ->setIsOfficial($data['internal']['official'] ?? true)
                ->setIsRectoVerso($data['internal']['rectoverso'])
                ->setFrontColor($frontPageColor)
                ->setIsPerforated($data['internal']['perforated'])
                ->setIsColored($colored)
                ->setIsHardCovered($data['internal']['hardcovered'] ?? false);
        }

        return $object;
    }
}
