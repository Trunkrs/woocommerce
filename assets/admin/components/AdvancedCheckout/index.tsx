import React from 'react'
import Switch from '../Switch'

interface AdvancedCheckoutProps {
  allOrdersAreTrunkrs: boolean
  onAllOrdersAreTrunkrs: () => void | Promise<void>
}

const AdvancedCheckout: React.FC<AdvancedCheckoutProps> = ({
  allOrdersAreTrunkrs,
  onAllOrdersAreTrunkrs,
}) => {
  return (
    <div className="tr-wc-detailsPanel">
      <span className="tr-wc-panelHeader">
        <p>Verzendings instellingen</p>
      </span>
      <span className="tr-wc-panelContent">
        <ul className="tr-wc-settingsList">
          <li>
            <Switch
              checked={allOrdersAreTrunkrs}
              onChange={onAllOrdersAreTrunkrs}
            >
              <h4 className="tr-wc-switchLabel">
                Alle orders zijn voor Trunkrs
              </h4>
            </Switch>
          </li>
        </ul>
      </span>
    </div>
  )
}

export default AdvancedCheckout
