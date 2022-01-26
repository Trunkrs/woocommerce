import React from 'react'

import useConfig from '../../providers/Config/useConfig'

import { LoginResult } from '../ConnectButton'
import TrunkrsFull from '../../../shared/components/vectors/TrunkrsFull'
import CenteredContainer from '../CenteredContainer'

import ConnectionPanel from '../ConnectionPanel'
import DetailsPanel from '../DetailsPanel'
import CheckoutPanel from '../CheckoutPanel'
import AdvancedCheckout from '../AdvancedCheckout'
import { RuleModel } from '../AdvancedCheckout/RulesModal'

import { fromOrderRuleString, toOrderRuleString } from './helpers'
import './AppContainer.scss'

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
    updateUseOrderRules,
    updateOrderRules,
  } = useConfig()

  const [isWorkingRules, setWorkingRules] = React.useState(false)

  const handleLoginDone = React.useCallback(
    async (result: LoginResult): Promise<void> =>
      prepareConfig(result.accessToken, result.organizationId),
    [prepareConfig],
  )

  const handleSaveRules = React.useCallback(
    async (rules: RuleModel[]) => {
      try {
        setWorkingRules(true)
        await updateOrderRules(toOrderRuleString(rules))
      } finally {
        setWorkingRules(false)
      }
    },
    [updateOrderRules],
  )

  const parsedRules = React.useMemo(
    () => fromOrderRuleString(config?.orderRules),
    [config?.orderRules],
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
            isLoading={isWorkingRules}
            rules={parsedRules}
            isOrderFiltersEnabled={config.isOrderRulesEnabled}
            allOrdersAreTrunkrs={config.isAllOrdersAreTrunkrsEnabled}
            onAllOrdersAreTrunkrs={updateAllOrdersAreTrunkrs}
            onEnableOrderRules={updateUseOrderRules}
            onSaveRules={handleSaveRules}
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
