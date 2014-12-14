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

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Article\External as ExternalArticle,
    CudiBundle\Entity\Article\Internal as InternalArticle,
    InvalidArgumentException;

class Article extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $article_keys = array(
        'title', 'year_published', 'isbn', 'url',
    );

    private static $internal_keys = array(
        'nb_black_and_white', 'nb_colored',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array(
            'article' => $this->stdExtract(
                $object,
                array(
                    self::$article_keys,
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
                    self::$internal_keys,
                    array('perforated', 'colored', 'hardcovered', 'official'),
                )
            );

            $data['internal']['binding'] = $object->getBinding() ? $object->getBinding()->getId() : '';
            $data['internal']['front_color'] = $object->getFrontColor() ? $object->getFrontColor()->getId() : '';
            $data['internal']['rectoverso'] = $object->isRectoVerso();
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
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
            $this->stdHydrate($data['article'], $object, self::$article_keys);

            $object->setAuthors($data['article']['authors'])
                ->setPublishers($data['article']['publisher'])
                ->setIsDownloadable($data['article']['downloadable'])
                ->setIsSameAsPreviousYear($data['article']['same_as_previous_year'])
                ->setType(isset($data['article']['type']) && '' != $data['article']['type'] ? $data['article']['type'] : 'common');
        }

        if ($object->isInternal() && isset($data['internal'])) {
            $binding = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                ->findOneById($data['internal']['binding']);

            $frontPageColor = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Color')
                ->findOneById($data['internal']['front_color']);

            $this->stdHydrate($data['internal'], $object, self::$internal_keys);

            $object->setBinding($binding)
                ->setIsOfficial($data['internal']['official'])
                ->setIsRectoVerso($data['internal']['rectoverso'])
                ->setFrontColor($frontPageColor)
                ->setIsPerforated($data['internal']['perforated'])
                ->setIsColored($data['internal']['colored'])
                ->setIsHardCovered($data['internal']['hardcovered']);
        }

        return $object;
    }
}
