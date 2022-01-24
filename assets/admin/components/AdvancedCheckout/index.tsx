import React from 'react'

import Switch from '../Switch'
import Panel from '../Panel'

interface AdvancedCheckoutProps {
  allOrdersAreTrunkrs: boolean
  onAllOrdersAreTrunkrs: () => void | Promise<void>
}

const AdvancedCheckout: React.FC<AdvancedCheckoutProps> = ({
  allOrdersAreTrunkrs,
  onAllOrdersAreTrunkrs,
}) => {
  return (
    <Panel title="Verzendings instellingen">
      <ul className="tr-wc-settingsList">
        <li>
          <Switch
            checked={allOrdersAreTrunkrs}
            onChange={onAllOrdersAreTrunkrs}
          >
            <h4 className="tr-wc-switchLabel">Alle orders zijn voor Trunkrs</h4>
          </Switch>
        </li>
      </ul>
    </Panel>
  )
}

export default AdvancedCheckout
