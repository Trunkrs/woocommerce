import React from 'react'
import Switch from '../Switch'

import './CheckoutPanel.scss'

interface CheckoutPanelProps {
  useBigText: boolean
  darkLogo: boolean
  tntEmailLinks: boolean
  tntAccountActions: boolean
  onUseBigTextChanged: () => void | Promise<void>
  onDarkLogoChanged: () => void | Promise<void>
  onTntEmailLinksChanged: () => void | Promise<void>
  onTntAccountActionsChanged: () => void | Promise<void>
}

const CheckoutPanel: React.FC<CheckoutPanelProps> = ({
  useBigText,
  darkLogo,
  tntEmailLinks,
  tntAccountActions,
  onUseBigTextChanged,
  onDarkLogoChanged,
  onTntEmailLinksChanged,
  onTntAccountActionsChanged,
}) => {
  return (
    <div className="tr-wc-detailsPanel">
      <span className="tr-wc-panelHeader">
        <p>Visuele instellingen</p>
      </span>
      <span className="tr-wc-panelContent">
        <ul className="tr-wc-settingsList">
          <li>
            <Switch checked={darkLogo} onChange={onDarkLogoChanged}>
              <h4 className="tr-wc-switchLabel">
                Gebruik aangepast logo voor donker thema in de winkelwagen.
              </h4>
            </Switch>
          </li>

          <li>
            <Switch checked={useBigText} onChange={onUseBigTextChanged}>
              <h4 className="tr-wc-switchLabel">
                Laat het verwachte levermoment zien in winkelwagen.
              </h4>
            </Switch>
          </li>

          <li>
            <Switch checked={tntEmailLinks} onChange={onTntEmailLinksChanged}>
              <h4 className="tr-wc-switchLabel">
                Plaats Trunkrs Track & Trace links in orderbevestiging e-mail.
              </h4>
            </Switch>
          </li>

          <li>
            <Switch
              checked={tntAccountActions}
              onChange={onTntAccountActionsChanged}
            >
              <h4 className="tr-wc-switchLabel">
                Maak Trunkrs Track & Trace links zichtbaar in gebruikers account
                pagina.
              </h4>
            </Switch>
          </li>
        </ul>
      </span>
    </div>
  )
}

export default CheckoutPanel
