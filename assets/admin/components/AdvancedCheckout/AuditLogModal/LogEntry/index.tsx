import React from 'react'

import { AuditLogEntry } from '../../../../providers/Config/helpers'

import RuleEntry from './RuleEntry'
import PlainEntry from './PlainEntry'

import './LogEntry.scss'

const LogEntry: React.FC<AuditLogEntry> = ({ orderId, timestamp, entries }) => (
  <table className="tr-wc-logEntry-table">
    <tbody>
      <tr>
        <td
          rowSpan={entries?.length ?? 1}
          width="25%"
          className="tr-wc-auditLogModal-dataCell"
        >
          <a
            href={`/wp-admin/post.php?post=${orderId}&action=edit`}
          >{`Bestelling #${orderId}`}</a>
          <p>{`${timestamp} UTC`}</p>
        </td>

        <td width="85%">
          <table width="100%" style={{ borderCollapse: 'collapse' }}>
            {(entries ?? []).map((entry) => {
              switch (entry.type) {
                case 'PLAIN_LOG':
                  return (
                    <tr
                      key={`${entry.type}-${entry.fieldName}-${entry.fieldValue}-${timestamp}`}
                    >
                      <PlainEntry {...entry} />
                    </tr>
                  )
                default:
                  return (
                    <tr
                      key={`${entry.type}-${entry.fieldName}-${entry.fieldValue}-${timestamp}`}
                    >
                      <RuleEntry {...entry} />
                    </tr>
                  )
              }
            })}
          </table>
        </td>
      </tr>
    </tbody>
  </table>
)

export default LogEntry
