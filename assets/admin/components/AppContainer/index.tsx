import React from 'react'

import useConfig from '../../providers/Config/useConfig'

import { LoginResult } from '../ConnectButton'
import TrunkrsFull from '../../../shared/components/vectors/TrunkrsFull'
import CenteredContainer from '../CenteredContainer'

import ConnectionPanel from '../ConnectionPanel'
import DetailsPanel from '../DetailsPanel'

import './AppContainer.scss'
import CheckoutPanel from '../CheckoutPanel'
import AdvancedCheckout from '../AdvancedCheckout'

const AppContainer: React.FC = () => {
  const {
    isWorking,
    config,
    prepareConfig,
    updateUseBigText,
    updateIsDarkLogo,
    updateTntLinks,
    updateTntActions,
    updateAllOrdersAreTrunkrs,
  } = useConfig()

  const handleLoginDone = React.useCallback(
    async (result: LoginResult): Promise<void> =>
      prepareConfig(result.accessToken, result.organizationId),
    [prepareConfig],
  )

  return (
    <CenteredContainer>
      <TrunkrsFull className="tr-wc-trunkrsFull" />

      {!config?.isConfigured ? (
        <ConnectionPanel loading={isWorking} onLoginDone={handleLoginDone} />
      ) : (
        <>
          <DetailsPanel
            integrationId={config.details.integrationId}
            organizationId={config.details.organizationId}
            organizationName={config.details.organizationName}
          />

          <AdvancedCheckout
            allOrdersAreTrunkrs={config.isAllOrdersAreTrunkrsEnabled}
            onAllOrdersAreTrunkrs={updateAllOrdersAreTrunkrs}
          />

          <CheckoutPanel
            useBigText={config.isBigTextEnabled}
            darkLogo={config.isDarkLogo}
            tntEmailLinks={config.isEmailLinksEnabled}
            tntAccountActions={config.isAccountTrackTraceEnabled}
            onUseBigTextChanged={updateUseBigText}
            onDarkLogoChanged={updateIsDarkLogo}
            onTntEmailLinksChanged={updateTntLinks}
            onTntAccountActionsChanged={updateTntActions}
          />
        </>
      )}
    </CenteredContainer>
  )
}

export default AppContainer
