<?php

namespace PageBundle\Component\Validator;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Util\Url;
use CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Matches the given page title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PageTitle extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var Form
     */
    private $form;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'There already exists a page with this title and the same parent',
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['exclude'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $parentName = null;
        if ($this->form->get('parent_' . $this->form->get('category')->getValue())->getValue() != '') {
            $realParent = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneById($this->form->get('parent_' . $this->form->get('category')->getValue())->getValue());

            $parentName = $realParent->getName();
        }

        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findOneByNameAndParent(
                Url::createSlug($value),
                Url::createSlug($parentName)
            );

        if ($page === null || $page->getName() == $this->options['exclude']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }

    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }
}
