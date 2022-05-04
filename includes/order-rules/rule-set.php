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
      if (empty($ruleSetString))
        return;

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
    public function matchOrder(TRUNKRS_WC_Order $wrapper, bool $withLog = false): bool
    {
      $auditLog = new TRUNKRS_WC_AuditLog($wrapper->order->get_id());

      try {
        if (!isset($this->fields))
          return false;

        $firstShippingItem = TRUNKRS_WC_Utils::firstInIterable($wrapper->order->get_items('shipping'));

        if ( !is_null($firstShippingItem) ) {
            $shipping = $firstShippingItem->get_data();
            $data = $wrapper->order->get_data();

            foreach ($this->fields as $field => $rules) {
            $value = key_exists($field, $data) ? $data[$field] : null;
            if (!isset($value))
                $value = key_exists($field, $shipping) ? $shipping[$field] : null;
            if (!isset($value))
                $value = $wrapper->order->get_meta($field);

            if (!isset($value)) {
                return false;
            }

            $booleanCounter = 0;

            foreach ($rules as $rule) {
                $matches = $rule->matches($value);
                $auditLog->createEntry($field, $value)->setResult($rule, $matches);

                $booleanCounter += $matches;
            }

            if ($booleanCounter === 0) {
                return false;
            }
            }

            return true;
        }
        
        return false;
      } finally {
        if ($withLog) {
          $auditLog->saveLog();
        }
      }
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

