import React from 'react'

import Filter from '../vectors/Filter'
import Book from '../vectors/Book'

import Switch from '../Switch'
import Panel from '../Panel'
import Button from '../Button'
import Modal from '../Modal'

import RulesModal from './RulesModal'

import './AdvancedCheckout.scss'

interface AdvancedCheckoutProps {
  allOrdersAreTrunkrs: boolean
  isOrderFiltersEnabled: boolean
  onAllOrdersAreTrunkrs: () => void | Promise<void>
  onEnableOrderRules: () => void | Promise<void>
}

const AdvancedCheckout: React.FC<AdvancedCheckoutProps> = ({
  allOrdersAreTrunkrs,
  isOrderFiltersEnabled,
  onAllOrdersAreTrunkrs,
  onEnableOrderRules,
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

      <RulesModal open={isRulesOpen} onClose={handleToggleRules}>
        <p>Some rule content</p>
      </RulesModal>

      <Modal open={isLogsOpen} onClose={handleToggleLogs}>
        <p>Some logs content</p>
      </Modal>
    </>
  )
}

export default AdvancedCheckout
