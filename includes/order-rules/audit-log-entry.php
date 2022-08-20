<?php

if (!class_exists('TRUNKRS_WC_AuditLogEntry')) {
  abstract class TRUNKRS_WC_AuditLogEntry {
    /**
     * The type of entry.
     * @var string
     */
    var $type;

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

    public function __construct(string $type, string $fieldName, $value) {
      $this->fieldName = $fieldName;
      $this->fieldValue = $value;
      $this->type = $type;
    }

    /**
     * Returns the contents of the entry as an array for serialization.
     * @return array
     */
    public abstract function asArray(): array;
  }
}
