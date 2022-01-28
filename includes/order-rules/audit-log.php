<?php

if (!class_exists('TRUNKRS_WC_AuditLog')) {
  class TRUNKRS_WC_AuditLog
  {
    /**
     * @see TRUNKRS_WC_InitDB::LOG_TABLE_NAME
     */
    private const LOG_TABLE_NAME = 'trunkrs_rule_audit_log';

    /**
     * Retrieve the latest 10 audit logs from the database.
     * @return TRUNKRS_WC_AuditLog[]
     */
    public static function findLatestAuditLogs()
    {
      global $wpdb;

      $tableName = $wpdb->prefix . self::LOG_TABLE_NAME;

      $results = $wpdb->get_results(
        "
        SELECT order_id,
               timestamp,
               json_data
        FROM $tableName
        ORDER BY timestamp DESC
        LIMIT 10;
        "
      );

      return array_map(function ($row) {
        return [
          'orderId' => $row->order_id,
          'timestamp' => $row->timestamp,
          'entries' => json_decode($row->json_data),
        ];
      }, $results);
    }

    /**
     * The order id of the evaluated order.
     * @var int
     */
    var $orderId;

    /**
     * The audit log entries
     * @var TRUNKRS_WC_AuditLogRuleEntry[]
     */
    var $entries;

    public function __construct(int $orderId)
    {
      $this->orderId = $orderId;
    }

    /**
     * Create a new audit log entry.
     * @return TRUNKRS_WC_AuditLogRuleEntry
     */
    public function createEntry(string $fieldName, $fieldValue)
    {
      return $this->entries[] = new TRUNKRS_WC_AuditLogRuleEntry($fieldName, $fieldValue);
    }

    /**
     * Save the log into the database.
     * @return void
     */
    public function saveLog()
    {
      global $wpdb;

      $wpdb->insert(
        $wpdb->prefix . self::LOG_TABLE_NAME,
        [
          'order_id' => $this->orderId,
          'json_data' => json_encode($this->asArray()['entries']),
        ],
        ['%d', '%s']
      );
    }

    /**
     * Yields an array representation of the audit log entry.
     * @return array
     */
    public function asArray(): array
    {
      return [
        'orderId' => $this->orderId,
        'entries' => array_map(function ($entry) {
          return [
            'fieldName' => $entry->fieldName,
            'fieldValue' => $entry->fieldValue,
            'comparisons' => $entry->results,
          ];
        }, $this->entries),
      ];
    }
  }
}

