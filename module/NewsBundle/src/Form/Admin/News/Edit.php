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

namespace NewsBundle\Form\Admin\News;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    NewsBundle\Entity\Nodes\News,
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \NewsBundle\Entity\Nodes\News $news The news item we're going to modify
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, News $news, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $form = $this->getSubForm('tab_content');

        foreach ($this->getLanguages() as $language) {
            $title = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('title_' . $language->getAbbrev());
            $title->clearValidators();
        }

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'news_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->_populateFromNews($news);
    }

    private function _populateFromNews(News $news)
    {
        $data = array();
        foreach($this->getLanguages() as $language) {
            $data['content_' . $language->getAbbrev()] = $news->getContent($language, false);
            $data['title_' . $language->getAbbrev()] = $news->getTitle($language, false);
        }

        $this->populate($data);
    }
}
