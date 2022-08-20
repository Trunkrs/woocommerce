import React from 'react'
import { AuditLogInnerEntry } from '../../../../providers/Config/helpers'

const PlainEntry: React.FC<AuditLogInnerEntry> = ({
  fieldName,
  fieldValue,
}) => (
  <td colSpan={2} className="tr-wc-auditLogModal-dataCell">
    <p style={{ fontWeight: 600 }}>{fieldName}</p>
    <p>{fieldValue}</p>
  </td>
)

export default PlainEntry
