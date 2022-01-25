import React from 'react'
import { Field, FieldProps } from 'formik'

import IconButton from '../../../IconButton'
import RemoveFilter from '../../../vectors/RemoveFilter'

import './Rule.scss'

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
              <option value="method_id">Verzendoptie code</option>
              <option value="method_title">Verzendoptie naam</option>
              <option value="payment_method">Betaalmethode type</option>
              <option value="payment_method_title">Betaalmethode naam</option>
              <option value="total">Order totaal</option>
              <option value="status">Orderstatus</option>
              <option value="created_via">Aangemaakt via</option>
            </select>
          )}
        />
        <Field
          name={`${prefix}.comparator`}
          render={({ field }: FieldProps<string>) => (
            <select className="tr-wc-Rule-select" {...field}>
              <option value="EQ">Gelijk aan</option>
              <option value="NQ">Niet gelijk aan</option>
              <option value="GT">Groter dan</option>
              <option value="LT">Kleiner dan</option>
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
