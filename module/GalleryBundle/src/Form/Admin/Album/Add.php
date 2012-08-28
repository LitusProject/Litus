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

namespace GalleryBundle\Form\Admin\Album;

use CommonBundle\Component\Form\Bootstrap\SubForm\TabContent,
    CommonBundle\Component\Form\Bootstrap\SubForm\TabPane,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Tabs,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Doctrine\ORM\EntityManager,
    GalleryBundle\Component\Validator\Name as NameValidator,
    GalleryBundle\Entity\Album\Album,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add an album.
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form\Tabbable
{
	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;

	/**
	 * @var \GalleryBundle\Entity\Album\Album
	 */
	protected $album;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
	 */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

		$this->_entityManager = $entityManager;

		$tabs = new Tabs('languages');
        $tabs->setAttribute('id', 'languages');
		$this->add($tabs);

        $tabContent = new TabContent('tabs-content');

		foreach($this->_getLanguages() as $language) {
		    $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

		    $pane = new TabPane('tab_' . $language->getAbbrev());

		    $field = new Text('title_' . $language->getAbbrev());
		    $field->setLabel('Title')
		        ->setAttribute('class', $field->getAttribute('class') . ' input-xxlarge')
		        ->setRequired();
		    $pane->add($field);

		    $tabContent->add($pane);
		}

		$this->add($tabContent);

		$field = new Text('date');
		$field->setLabel('Date')
		    ->setAttribute('class', $field->getAttribute('class') . ' input-large')
		    ->setRequired();
		$this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add');
        $this->add($field);
    }

    public function populateFromAlbum(Album $album)
    {
        $data = array(
            'date' => $album->getDate()->format('d/m/Y'),
        );
        foreach($this->_getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $album->getTitle($language);
        }
        $this->setData($data);
    }

    protected function _getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            foreach($this->_getLanguages() as $language) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'title_' . $language->getAbbrev(),
                            'required' => true,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new NameValidator($this->_entityManager),
                            ),
                        )
                    )
                );
            }

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'date',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'date',
                                'options' => array(
                                    'format' => 'd/m/Y',
                                ),
                            ),
                        ),
                    )
                )
            );
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
