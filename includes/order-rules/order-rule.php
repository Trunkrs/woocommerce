<?php

if (!class_exists('TRUNKRS_WC_OrderRule')) {
  class TRUNKRS_WC_OrderRule
  {
    private const RULE_SEPARATOR = '|';

    /**
     * @see TRUNKRS_WC_RuleOperator
     * @var string The operator used for comparisons.
     */
    var $operator;

    /**
     * @var string The value to check against.
     */
    var $value;

    public function __construct(string $operatorString)
    {
      $values = explode('|', self::RULE_SEPARATOR);

      $this->operator = $values[0];
      $this->value = $values[1];
    }

    /**
     * Checks whether the value matches the field.
     * @param string $value The field value
     * @return bool A value reflecting whether the field matches the rule condition.
     */
    public function matches(string $value): bool
    {
      switch ($this->operator) {
        case TRUNKRS_WC_RuleOperator::EQUALS:
          return $value == $this->value;

        case TRUNKRS_WC_RuleOperator::NOT_EQUALS:
          return $value != $this->value;

        case TRUNKRS_WC_RuleOperator::CONTAINS:
          return strpos($value, $this->value) !== false;

        case TRUNKRS_WC_RuleOperator::STARTS_WITH:
          return substr($value, 0, strlen($this->value)) === $this->value;

        case TRUNKRS_WC_RuleOperator::ENDS_WITH:
          return substr($value, -strlen($this->value)) === $this->value;

        default:
          return false;
      }
    }

    /**
     * Converts the rule into a string representation.
     * @return string Converts the rule to string
     */
    public function toString(): string
    {
      return $this->operator . self::RULE_SEPARATOR . $this->value;
    }
  }
}

