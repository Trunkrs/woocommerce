<?php

if (!class_exists('TRUNKRS_WC_AuditLogPlainEntry')) {
  class TRUNKRS_WC_AuditLogPlainEntry extends TRUNKRS_WC_AuditLogEntry {
    public function __construct(string $fieldName, $value)
    {
      parent::__construct(TRUNKRS_WC_LogType::PLAIN_LOG, $fieldName, $value);
    }

    public function asArray(): array
    {
      return [
        'type' => $this->type,
        'fieldName' => $this->fieldName,
        'fieldValue' => $this->fieldValue,
      ];
    }
  }
}
