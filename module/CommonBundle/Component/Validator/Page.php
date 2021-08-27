<?php

namespace CommonBundle\Component\Validator;

/**
 * Checks whether an Page is already subscribed to a FAQ
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Page extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'faq' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This page already has been subscribed to this FAQ',
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
            $options['faq'] = array_shift($args);
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

        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findOneById(intval($context['id']));

        $faq = $this->options['faq'];

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByFAQQuery($faq)->getResult();

        foreach ($maps as $map) {
            if ($map->getPage()->getId() === $page->getId()) {
                $this->error(self::NOT_VALID);
                return false;
            }
        }
        return true;
    }
}
