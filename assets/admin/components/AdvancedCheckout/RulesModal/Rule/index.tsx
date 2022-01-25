import React from 'react'
import { Field, FieldProps } from 'formik'

import './Rule.scss'
import IconButton from '../../../IconButton'
import RemoveFilter from '../../../vectors/RemoveFilter'

interface RuleProps {
  prefix: string
  onRemove: () => void
}

const Rule: React.FC<RuleProps> = ({ prefix, onRemove }) => {
  return (
    <div className="tr-wc-Rule-container">
      <div className="tr-wc-Rule-ruleContainer">
        <Field
          name={`${prefix}.field`}
          render={({ field }: FieldProps<string>) => (
            <select className="tr-wc-Rule-select" {...field}>
              <option value="EQ">Gelijk aan</option>
              <option value="NQ">Niet gelijk aan</option>
              <option value="CO">Bevat</option>
              <option value="SW">Begint met</option>
              <option value="EW">Eindigt met</option>
            </select>
          )}
        />
        <Field
          name={`${prefix}.comparator`}
          render={({ field }: FieldProps<string>) => (
            <select className="tr-wc-Rule-select" {...field}>
              <option value="EQ">Gelijk aan</option>
              <option value="NQ">Niet gelijk aan</option>
              <option value="CO">Bevat</option>
              <option value="SW">Begint met</option>
              <option value="EW">Eindigt met</option>
            </select>
          )}
        />
        <Field className="tr-wc-Rule-input" name={`${prefix}.value`} />
      </div>

      <div>
        <IconButton icon={<RemoveFilter />} onClick={onRemove} />
      </div>
    </div>
  )
}

export default Rule
