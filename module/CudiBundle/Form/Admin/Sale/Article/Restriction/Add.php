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

namespace CudiBundle\Form\Admin\Sale\Article\Restriction;




use CommonBundle\Component\Util\AcademicYear,
    CudiBundle\Component\Validator\Sale\Article\Restriction\Exists as RestrictionValidator,
    CudiBundle\Entity\Sale\Article,
    LogicException;

/**
 * Add Restriction
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Article
     */
    protected $article;

    public function init()
    {
        if (null === $this->article) {
            throw new LogicException('Cannot add a restriction to a null article');
        }

        parent::init();

        $this->addClass('restriction');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'id'     => 'restriction_type',
                'data-help' => 'Limit the sale of this article on user base:
                    <ul>
                        <li><b>Amount:</b> restrict the number of this article sold to this user</li>
                        <li><b>Member:</b> restrict this article to members only</li>
                        <li><b>Study:</b> restrict this article to students of one ore more studies</li>
                    </ul>',
                'options'   => array(
                    'amount' => 'Amount',
                    'member' => 'Member',
                    'study'  => 'Study',
                ),
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new RestrictionValidator($this->article, $this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'value',
            'attributes' => array(
                'class' => 'values',
            ),
            'elements'   => array(
                array(
                    'type'       => 'text',
                    'name'       => 'amount',
                    'label'      => 'Amount',
                    'required'   => true,
                    'attributes' => array(
                        'class' => 'value_amount',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validator' => array(
                                array('name' => 'int'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'checkbox',
                    'name'       => 'member',
                    'label'      => 'Member',
                    'attributes' => array(
                        'class' => 'value_member',
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'study',
                    'label'      => 'Study',
                    'required'   => true,
                    'attributes' => array(
                        'class'    => 'value_study',
                        'multiple' => true,
                        'options'  => $this->getStudies(),
                        'style'    => 'max-width: 100%;',
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Article $article
     * @return self
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    private function getStudies()
    {
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager());

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($academicYear);

        $options = array();
        foreach ($studies as $study) {
            $options[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();
        }

        return $options;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['value']['amount']['required'] = 'amount' === $this->data['type'];
        $specs['value']['member']['required'] = 'member' === $this->data['type'];
        $specs['value']['study']['required'] = 'study' === $this->data['type'];

        return $specs;
    }
}
