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

namespace CudiBundle\Form\Admin\Sale\Article\Discount;

use CommonBundle\Component\Validator\Price as PriceValidator,
    CudiBundle\Component\Validator\Sale\Article\Discount\Exists as DiscountValidator,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    LogicException;

/**
 * Add Discount
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
            throw new LogicException('Cannot add a discount to a null article');
        }

        parent::init();

        $this->addClass('discount');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'template',
            'label'      => 'Template',
            'required'   => true,
            'attributes' => array(
                'class'   => 'template',
                'options' => $this->getTemplates(),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'value',
            'label'      => 'Value',
            'required'   => true,
            'attributes' => array(
                'class' => 'element value',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'method',
            'label'      => 'Method',
            'required'   => true,
            'attributes' => array(
                'class'     => 'element method',
                'data-help' => 'The method of this discount:
                    <ul>
                        <li><b>Percentage:</b> the value will used as the percentage to substract from the real price</li>
                        <li><b>Fixed:</b> the value will be subtracted from the real price</li>
                        <li><b>Override:</b> the value will be used as the new price</li>
                    </ul>',
                'options'   => Discount::$POSSIBLE_METHODS,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'class'   => 'element type',
                'options' => Discount::$POSSIBLE_TYPES,
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new DiscountValidator($this->article, $this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'organization',
            'label'      => 'Organization',
            'required'   => true,
            'attributes' => array(
                'class'   => 'element organization',
                'options' => $this->getOrganizations(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'rounding',
            'label'      => 'Rounding',
            'required'   => true,
            'attributes' => array(
                'class'   => 'element rounding',
                'options' => $this->getRoundings(),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'apply_once',
            'label'      => 'Apply Once',
            'attributes' => array(
                'class'     => 'element apply-once',
                'data-help' => 'Enabling this option will allow apply this discount only once to every user.',
            ),
        ));

        $this->addSubmit('Add', 'discount_add');
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

    private function getTemplates()
    {
        $templates = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();

        $templateOptions = array(0 => 'none');
        foreach ($templates as $template) {
            $templateOptions[$template->getId()] = $template->getName();
        }

        return $templateOptions;
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsOptions = array(0 => 'All');
        foreach ($organizations as $organization) {
            $organizationsOptions[$organization->getId()] = $organization->getName();
        }

        return $organizationsOptions;
    }

    private function getRoundings()
    {
        $roundings = array();
        foreach (Discount::$POSSIBLE_ROUNDINGS as $key => $rounding) {
            $roundings[$key] = $rounding['name'];
        }

        return $roundings;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $required = isset($this->data['template']) && 0 == $this->data['template'];

        $specs['value']['required'] = $required;
        $specs['method']['required'] = $required;
        $specs['type']['required'] = $required;
        $specs['organization']['required'] = $required;
        $specs['rounding']['required'] = $required;
        $specs['apply_once']['required'] = $required;

        return $specs;
    }
}
