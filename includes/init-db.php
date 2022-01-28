<?php

if (!class_exists('TRUNKRS_WC_InitDB')) {
  class TRUNKRS_WC_InitDB
  {
    /**
     * @see TRUNKRS_WC_AuditLog::LOG_TABLE_NAME
     */
    private const LOG_TABLE_NAME = 'trunkrs_rule_audit_log';

    private static function initAuditLog()
    {
      global $wpdb;
      $charset_collate = $wpdb->get_charset_collate();
      $table_name = $wpdb->prefix . self::LOG_TABLE_NAME;

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		            order_id int NOT NULL,
		            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		            json_data text NOT NULL,
		            PRIMARY KEY (order_id, timestamp)
	            ) $charset_collate;";

      dbDelta($sql);
    }

    public static function init()
    {
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      self::initAuditLog();
    }
  }
}
