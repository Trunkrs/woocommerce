import { RuleModel } from '../AdvancedCheckout/RulesModal'

const fieldSeparator = '%'
const valueSeparator = '$'
const ruleSeparator = '|'

export const fromOrderRuleString = (orderRuleString?: string): RuleModel[] => {
  if (!orderRuleString) return []

  const fieldStrings = orderRuleString.split(fieldSeparator)

  return fieldStrings.map((fieldString) => {
    const [fieldName, ruleString] = fieldString.split(valueSeparator)
    const [operator, value] = ruleString.split(ruleSeparator)

    return {
      field: fieldName,
      comparator: operator,
      value,
    }
  })
}

export const toOrderRuleString = (rules: RuleModel[]): string => {
  return rules
    .map((rule) =>
      [rule.field, [rule.comparator, rule.value].join(ruleSeparator)].join(
        valueSeparator,
      ),
    )
    .join(fieldSeparator)
}
