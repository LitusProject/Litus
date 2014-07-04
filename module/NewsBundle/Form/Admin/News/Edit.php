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

namespace NewsBundle\Form\Admin\News;

use Doctrine\ORM\EntityManager,
    NewsBundle\Entity\Node\News,
    Zend\Form\Element\Submit;

/**
 * Edit News
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param News            $news          The news item we're going to modify
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, News $news, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'news_edit');
        $this->add($field);

        $this->_populateFromNews($news);
    }

    private function _populateFromNews(News $news)
    {
        $data = array(
            'end_date' => $news->getEndDate() ? $news->getEndDate()->format('d/m/Y H:i') : '',
        );
        foreach ($this->getLanguages() as $language) {
            $data['content_' . $language->getAbbrev()] = $news->getContent($language, false);
            $data['title_' . $language->getAbbrev()] = $news->getTitle($language, false);
        }

        $this->setData($data);
    }
}
