import React from 'react'

import { AuditLogEntry } from '../../../../providers/Config/helpers'

import './LogEntry.scss'

const opLabels = {
  EQ: 'gelijk aan',
  NQ: 'niet gelijk aan',
  GT: 'groter dan',
  LT: 'kleiner dan',
  CO: 'bevat',
  SW: 'begint met',
  EW: 'eindigt met',
}

const LogEntry: React.FC<AuditLogEntry> = ({ orderId, timestamp, entries }) => (
  <table className="tr-wc-logEntry-table">
    <tbody>
      {entries.map((entry, index) =>
        entry.comparisons.map((comparison) => (
          <tr
            key={`${entry.fieldName}-${comparison.operator}-${comparison.compareValue}`}
          >
            {index === 0 && (
              <td
                rowSpan={entries.length}
                width="25%"
                className="tr-wc-auditLogModal-dataCell"
              >
                <a
                  href={`/wp-admin/post.php?post=${orderId}&action=edit`}
                >{`Bestelling #${orderId}`}</a>
                <p>{`${timestamp} UTC`}</p>
              </td>
            )}

            <td width="45%" className="tr-wc-auditLogModal-dataCell">
              {`${entry.fieldName} ${
                opLabels[comparison.operator as keyof typeof opLabels]
              } ${comparison.compareValue}`}
            </td>
            <td width="40%" className="tr-wc-auditLogModal-dataCell">
              <p>Waarde: {entry.fieldValue}</p>
              <p>
                Uitkomst:{' '}
                <span style={{ color: comparison.result ? 'green' : 'red' }}>
                  {String(comparison.result)}
                </span>
              </p>
            </td>
          </tr>
        )),
      )}
    </tbody>
  </table>
)

export default LogEntry
