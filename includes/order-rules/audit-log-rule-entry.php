<?php

if (!class_exists('TRUNKRS_WC_AuditLogRuleEntry')) {
  class TRUNKRS_WC_AuditLogRuleEntry extends TRUNKRS_WC_AuditLogEntry
  {
    /**
     * The results of the evaluation.
     * @var array
     */
    var $results;

    public function __construct(string $fieldName, $value)
    {
      parent::__construct(TRUNKRS_WC_LogType::ORDER_MATCH, $fieldName, $value);
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

    public function asArray(): array
    {
      return [
        'type' => $this->type,
        'fieldName' => $this->fieldName,
        'fieldValue' => $this->fieldValue,
        'comparisons' => $this->results,
      ];
    }
  }
}

