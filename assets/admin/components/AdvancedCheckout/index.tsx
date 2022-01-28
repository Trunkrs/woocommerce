import React from 'react'

import Filter from '../vectors/Filter'
import Book from '../vectors/Book'

import Switch from '../Switch'
import Panel from '../Panel'
import Button from '../Button'

import RulesModal, { RuleModel } from './RulesModal'

import './AdvancedCheckout.scss'
import AuditLogModal from './AuditLogModal'

interface AdvancedCheckoutProps {
  isLoading: boolean
  rules: RuleModel[]
  isSubRenewalsEnabled: boolean
  allOrdersAreTrunkrs: boolean
  isOrderFiltersEnabled: boolean
  onAllOrdersAreTrunkrs: () => void | Promise<void>
  onSaveRules: (rules: RuleModel[]) => void | Promise<void>
  onEnableOrderRules: () => void | Promise<void>
  onIsSubRenewalsEnabled: () => void | Promise<void>
}

const AdvancedCheckout: React.FC<AdvancedCheckoutProps> = ({
  isLoading,
  rules,
  isSubRenewalsEnabled,
  allOrdersAreTrunkrs,
  isOrderFiltersEnabled,
  onAllOrdersAreTrunkrs,
  onEnableOrderRules,
  onSaveRules,
  onIsSubRenewalsEnabled,
}) => {
  const [isRulesOpen, setRulesOpen] = React.useState(false)
  const [isLogsOpen, setLogsOpen] = React.useState(false)

  const handleToggleRules = React.useCallback(() => {
    setRulesOpen((current) => !current)
  }, [])

  const handleToggleLogs = React.useCallback(() => {
    setLogsOpen((current) => !current)
  }, [])

  return (
    <>
      <Panel title="Verzendings instellingen">
        <ul className="tr-wc-settingsList">
          <li>
            <Switch
              checked={isSubRenewalsEnabled}
              onChange={onIsSubRenewalsEnabled}
            >
              <h4 className="tr-wc-switchLabel">
                Maak een zending aan als abonnement verlenging is betaald.
              </h4>
            </Switch>
          </li>

          <li>
            <Switch
              disabled={isOrderFiltersEnabled}
              checked={allOrdersAreTrunkrs}
              onChange={onAllOrdersAreTrunkrs}
            >
              <h4 className="tr-wc-switchLabel">
                Alle orders zijn exclusief voor Trunkrs.
              </h4>
            </Switch>
          </li>

          <li>
            <Switch
              disabled={allOrdersAreTrunkrs}
              checked={isOrderFiltersEnabled}
              onChange={onEnableOrderRules}
            >
              <h4 className="tr-wc-switchLabel">
                Gebruik order selectie filters.
              </h4>
            </Switch>
          </li>

          <li className="tr-wc-ruleBtn-group">
            <Button
              color="white"
              size="small"
              className="tr-wc-ruleBtn"
              disabled={!isOrderFiltersEnabled}
              onClick={handleToggleRules}
            >
              <Filter className="tr-wc-buttonVector" />
              Selectie regels
            </Button>

            <Button
              color="white"
              size="small"
              disabled={!isOrderFiltersEnabled}
              onClick={handleToggleLogs}
            >
              <Book className="tr-wc-buttonVector" />
              Logboek
            </Button>
          </li>
        </ul>
      </Panel>

      <RulesModal
        isLoading={isLoading}
        open={isRulesOpen}
        rules={rules}
        onClose={handleToggleRules}
        onSaveRules={onSaveRules}
      />

      <AuditLogModal open={isLogsOpen} onClose={handleToggleLogs} />
    </>
  )
}

export default AdvancedCheckout
