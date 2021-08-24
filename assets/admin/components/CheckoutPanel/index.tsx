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
        <p>Visuele instellingen</p>
      </span>
      <span className="tr-wc-panelContent">
        <Switch checked={darkLogo} onChange={onDarkLogoChanged}>
          <h4 className="tr-wc-switchLabel">
            Gebruik aangepast logo voor donker thema in de winkelwagen.
          </h4>
        </Switch>
      </span>
    </div>
  )
}

export default CheckoutPanel
