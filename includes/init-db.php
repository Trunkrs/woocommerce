<?php

if (!class_exists('TRUNKRS_WC_InitDB')) {
  class TRUNKRS_WC_InitDB
  {
    public const LOG_TABLE_NAME = 'trunkrs_rule_audit_log_v3';

    public const INIT_QUERY = "
      CREATE TABLE IF NOT EXISTS %s (
        order_id int NOT NULL,
        timestamp timestamp(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
        json_data text NOT NULL,
        PRIMARY KEY (order_id, timestamp)
      );
    ";

    public static function getCreateTableQuery() {
      global $wpdb;
      $charsetCollate = $wpdb->get_charset_collate();
      $tableName = $wpdb->prefix . self::LOG_TABLE_NAME;
      $sql = sprintf(self::INIT_QUERY, $tableName);

      return $sql;
    }

    private static function initAuditLog()
    {
      dbDelta(self::getCreateTableQuery());
    }

    public static function init()
    {
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      self::initAuditLog();
    }
  }
}
