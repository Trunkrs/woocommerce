import React from 'react'
import { AuditLogInnerEntry } from '../../../../providers/Config/helpers'

const opLabels = {
  EQ: 'gelijk aan',
  NQ: 'niet gelijk aan',
  GT: 'groter dan',
  LT: 'kleiner dan',
  CO: 'bevat',
  SW: 'begint met',
  EW: 'eindigt met',
}

const RuleEntry: React.FC<AuditLogInnerEntry> = ({
  fieldValue,
  fieldName,
  comparisons,
}) => (
  <>
    {(comparisons ?? []).map((comparison) => (
      <React.Fragment
        key={`${fieldName}-${comparison.operator}-${comparison.compareValue}`}
      >
        <td width="65%" className="tr-wc-auditLogModal-dataCell">
          {`${fieldName} ${
            opLabels[comparison.operator as keyof typeof opLabels]
          } ${comparison.compareValue}`}
        </td>
        <td width="35%" className="tr-wc-auditLogModal-dataCell">
          <p>Waarde: {fieldValue}</p>
          <p>
            Uitkomst:{' '}
            <span style={{ color: comparison.result ? 'green' : 'red' }}>
              {String(comparison.result)}
            </span>
          </p>
        </td>
      </React.Fragment>
    ))}
  </>
)

export default RuleEntry
