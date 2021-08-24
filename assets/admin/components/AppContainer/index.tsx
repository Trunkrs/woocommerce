import React from 'react'

import useConfig from '../../providers/Config/useConfig'

import { LoginResult } from '../ConnectButton'
import TrunkrsFull from '../../../shared/components/vectors/TrunkrsFull'
import CenteredContainer from '../CenteredContainer'

import ConnectionPanel from '../ConnectionPanel'
import DetailsPanel from '../DetailsPanel'

import './AppContainer.scss'
import CheckoutPanel from '../CheckoutPanel'

const AppContainer: React.FC = () => {
  const { isWorking, config, prepareConfig, updateIsDarkLogo } = useConfig()

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

          <CheckoutPanel
            darkLogo={config.isDarkLogo}
            onDarkLogoChanged={updateIsDarkLogo}
          />
        </>
      )}
    </CenteredContainer>
  )
}

export default AppContainer