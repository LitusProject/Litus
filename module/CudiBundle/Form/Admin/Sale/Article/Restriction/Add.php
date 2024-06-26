<?php

namespace CudiBundle\Form\Admin\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CudiBundle\Entity\Sale\Article;
use LogicException;

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
        if ($this->article === null) {
            throw new LogicException('Cannot add a restriction to a null article');
        }

        parent::init();

        $this->addClass('restriction');

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'id'        => 'restriction_type',
                    'data-help' => 'Limit the sale of this article on user base:
                    <ul>
                        <li><b>Amount:</b> restrict the number of this article sold to this user</li>
                        <li><b>Member:</b> restrict this article to members only</li>
                        <li><b>Study:</b> restrict this article to students of one ore more studies</li>
                    </ul>',
                    'options'   => array(
                        'amount'    => 'Amount',
                        'available' => 'Available',
                        'member'    => 'Member',
                        'study'     => 'Study',
                    ),
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'SaleArticleRestrictionExists',
                                'options' => array(
                                    'article' => $this->article,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
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
                                'filters' => array(
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
            )
        );

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
            ->findAllByAcademicYear($academicYear);

        $options = array();
        foreach ($studies as $study) {
            $options[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle();
        }

        return $options;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['value']['amount']['required'] = $this->data['type'] === 'amount';
        $specs['value']['member']['required'] = $this->data['type'] === 'member';
        $specs['value']['study']['required'] = $this->data['type'] === 'study';

        return $specs;
    }
}
