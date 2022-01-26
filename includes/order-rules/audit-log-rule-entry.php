<?php

if (!class_exists('TRUNKRS_WC_AuditLogRuleEntry')) {
  class TRUNKRS_WC_AuditLogRuleEntry
  {
    /**
     * The name of the field.
     * @var string
     */
    var $fieldName;

    /**
     * The value of the field.
     * @var mixed
     */
    var $fieldValue;

    /**
     * The results of the evaluation.
     * @var array
     */
    var $results;

    public function __construct(string $fieldName, $value) {
      $this->fieldName = $fieldName;
      $this->fieldValue = $value;
    }

    /**
     * Sets the result of the rule matching.
     * @param TRUNKRS_WC_OrderRule $rule The evaluated rule.
     * @param bool $result The execution result.
     * @return void
     */
    public function setResult(TRUNKRS_WC_OrderRule $rule, bool $result) {
      $this->results[] = [
        'operator' => $rule->operator,
        'compareValue' => $rule->value,
        'result' => $result
      ];
    }
  }
}

