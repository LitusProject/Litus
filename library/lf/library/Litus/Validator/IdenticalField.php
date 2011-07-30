<?php

namespace Litus\Validator;

/**
 * Based upon {@link http://emanaton.com/code/php/validateidenticalfield}
 */
class IdenticalField extends \Zend\Validator\AbstractValidator
{
    const NOT_MATCH = 'notMatch';
    const MISSING_FIELD_NAME = 'missingFieldName';
    const INVALID_FIELD_NAME = 'invalidFieldName';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::MISSING_FIELD_NAME => 'No field name was provided to check against',
        self::INVALID_FIELD_NAME => 'The field "%fieldName%" was not provided to match against',
        self::NOT_MATCH => 'The entered values do not match'
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'fieldName' => '_fieldName',
        'fieldTitle' => '_fieldTitle'
    );

    /**
     * Name of the field as it appears in the context array.
     *
     * @var string
     */
    protected $_fieldName = '';

    /**
     * Title of the field to display in an error message.
     * If this value is empty, it will be set to the value of $this->_fieldName.
     *
     * @var string
     */
    protected $_fieldTitle = '';

    /**
     * @param string $fieldName The name of the field that should be matched against
     * @param string $fieldTitle The title of the field that should be matched against
     */
    public function __construct($fieldName, $fieldTitle = '')
    {
        $this->_fieldName = $fieldName;
        $this->_fieldTitle = $fieldTitle;
    }

    /**
     * @param string $fieldName
     * @return \Litus\Validator\IdenticalField
     */
    public function setFieldName($fieldName)
    {
        $this->_fieldName = $fieldName;
        return $this;
    }

    /**
     * @param string $fieldTitle
     * @return \Litus\Validator\IdenticalField
     */
    public function setFieldTitle($fieldTitle = '')
    {
        $this->_fieldTitle = $fieldTitle;
        return $this;
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field name matches the provided value.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        if ('' == $this->_fieldTitle) {
            $this->_fieldTitle = $this->_fieldName;
        }

        if (empty($this->_fieldName)) {
            $this->_error(self::MISSING_FIELD_NAME);
            return false;
        } elseif (!isset($context[$this->_fieldName])) {
            $this->_error(self::INVALID_FIELD_NAME);
            return false;
        } elseif (is_array($context)) {
            if ($value == $context[$this->_fieldName]) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }
        $this->_error(self::NOT_MATCH);
        return false;
    }
}