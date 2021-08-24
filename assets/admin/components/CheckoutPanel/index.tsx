import React from 'react'
import Switch from '../Switch'

interface CheckoutPanelProps {
  darkLogo: boolean
  onDarkLogoChanged: () => void | Promise<void>
}

const CheckoutPanel: React.FC<CheckoutPanelProps> = ({
  darkLogo,
  onDarkLogoChanged,
}) => {
  return (
    <div className="tr-wc-detailsPanel">
      <span className="tr-wc-panelHeader">
        <p>Visual settings</p>
      </span>
      <span className="tr-wc-panelContent">
        <Switch checked={darkLogo} onChange={onDarkLogoChanged}>
          <h4 className="tr-wc-switchLabel">
            Use logo adjusted for darker themes on the check-out page.
          </h4>
        </Switch>
      </span>
    </div>
  )
}

export default CheckoutPanel
