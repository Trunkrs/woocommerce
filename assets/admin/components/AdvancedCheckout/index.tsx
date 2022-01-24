import React from 'react'

import Filter from '../vectors/Filter'
import Book from '../vectors/Book'

import Switch from '../Switch'
import Panel from '../Panel'
import Button from '../Button'

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
  return (
    <Panel title="Verzendings instellingen">
      <ul className="tr-wc-settingsList">
        <li>
          <Switch
            disabled={isOrderFiltersEnabled}
            checked={allOrdersAreTrunkrs}
            onChange={onAllOrdersAreTrunkrs}
          >
            <h4 className="tr-wc-switchLabel">Alle orders zijn voor Trunkrs</h4>
          </Switch>
        </li>

        <li>
          <Switch
            disabled={allOrdersAreTrunkrs}
            checked={isOrderFiltersEnabled}
            onChange={onEnableOrderRules}
          >
            <h4 className="tr-wc-switchLabel">Gebruik order filters</h4>
          </Switch>
        </li>

        <li className="tr-wc-ruleBtn-group">
          <Button
            color="white"
            className="tr-wc-ruleBtn"
            disabled={!isOrderFiltersEnabled}
          >
            <Filter className="tr-wc-buttonVector" />
            Order regels
          </Button>

          <Button disabled={!isOrderFiltersEnabled} color="white">
            <Book className="tr-wc-buttonVector" />
            Logboek
          </Button>
        </li>
      </ul>
    </Panel>
  )
}

export default AdvancedCheckout
