import React from 'react'
import { useField } from 'formik'

import './FieldInput.scss'

interface FieldInputProps {
  name: string
}

const preSelectable = {
  method_id: 'Verzendoptie code',
  method_title: 'Verzendoptie naam',
  payment_method: 'Betaalmethode type',
  payment_method_title: 'Betaalmethode naam',
  total: 'Order totaal',
  status: 'Order status',
  created_via: 'Aangemaakt via',
}
const selectableKeys = Object.keys(preSelectable) as Array<
  keyof typeof preSelectable
>

const FieldInput: React.FC<FieldInputProps> = ({ name }) => {
  const [field, , helpers] = useField(name)
  const [isEditable, setEditable] = React.useState(
    field.value && !selectableKeys.includes(field.value),
  )

  const handleSelectChanged = React.useCallback(
    (event: React.ChangeEvent<HTMLSelectElement>) => {
      const {
        target: { value },
      } = event

      const isNormalSelection = selectableKeys.includes(
        value as keyof typeof preSelectable,
      )
      if (isNormalSelection) {
        helpers.setValue(value)
        if (isEditable) {
          setEditable(false)
        }
      } else {
        helpers.setValue('')
        setEditable(true)
      }
    },
    [helpers, isEditable],
  )

  return (
    <div className="tr-wc-fieldInput-container">
      <select
        className="tr-wc-Rule-select"
        value={field.value}
        onBlur={field.onBlur}
        onChange={handleSelectChanged}
      >
        <option value="" hidden>
          Selecteer veld
        </option>
        {React.useMemo(
          () =>
            selectableKeys.map((key) => {
              const { [key]: label } = preSelectable
              return (
                <option key={key} value={key}>
                  {label}
                </option>
              )
            }),
          [],
        )}
        <option value="custom-field">Aangepaste waarde</option>
      </select>

      <input
        disabled={!isEditable}
        type="text"
        className="tr-wc-Rule-input tr-wc-fieldInput-input"
        placeholder={
          isEditable ? 'Type de naam van het veld' : 'Selecteer een veld'
        }
        value={field.value}
        name={field.name}
        onBlur={field.onBlur}
        onChange={field.onChange}
      />
    </div>
  )
}

export default FieldInput
