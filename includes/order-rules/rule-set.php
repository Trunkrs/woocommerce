<?php

if (!class_exists('TRUNKRS_WC_RuleSet')) {
  class TRUNKRS_WC_RuleSet
  {
    private const FIELD_SEPARATOR = '%';
    private const VALUE_SEPARATOR = '$';

    /**
     * The fields covered by this rule set.
     * @var array The rule set.
     */
    var $fields;

    public function __construct(string $ruleSetString)
    {
      $fieldStrings = explode(self::FIELD_SEPARATOR, $ruleSetString);

      $this->fields = array_reduce($fieldStrings, function ($fields, $field) {
        $value = explode(self::VALUE_SEPARATOR, $field);
        $fieldName = $value[0];
        $fieldRule = new TRUNKRS_WC_OrderRule($value[1]);

        if (!isset($fields[$fieldName])) {
          $fields[$fieldName] = [$fieldRule];
        } else {
          $fields[$fieldName][] = $fieldRule;
        }

        return $fields;
      }, []);
    }

    /**
     * Checks whether the order matches all rules in the set.
     * @param TRUNKRS_WC_Order $wrapper The order to check against.
     * @return bool Value reflecting whether the rule set matches the order.
     */
    public function matchOrder(TRUNKRS_WC_Order $wrapper): bool
    {
      $meta = $wrapper->order->get_meta_data();
      $data = $wrapper->order->get_data();

      foreach ($this->fields as $field => $rules) {
        $value = $data[$field] ?? $meta[$field];
        if (!isset($value)) {
          return false;
        }

        foreach ($rules as $rule) {
          if (!$rule->matches($value)) {
            return false;
          }
        }
      }

      return true;
    }

    /**
     * Converts the rule set into a string representation.
     * @return string The converted string representation.
     */
    public function toString(): string
    {
      $mappedFields = array_map(function ($rules, $fieldName) {
        $mappedRules = array_map(function ($rule) use ($fieldName) {
          return $fieldName . self::VALUE_SEPARATOR . $rule->toString();
        }, $rules);

        return implode(self::VALUE_SEPARATOR, $mappedRules);
      }, $this->fields, array_keys($this->fields));

      return implode(self::FIELD_SEPARATOR, $mappedFields);
    }
  }
}

