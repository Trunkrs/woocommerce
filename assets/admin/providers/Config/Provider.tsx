import React from 'react'
import { AxiosError } from 'axios'

import ConfigContext from '.'
import {
  doConfigureRequest,
  doShippingReqisterRequest,
  doUpdateUseDarkRequest,
} from './helpers'

const initialConfigText = document.getElementById('__tr-wc-settings__')
  ?.innerText as string
const initialConfig = initialConfigText ? JSON.parse(initialConfigText) : {}

const ConfigProvider: React.FC = ({ children }) => {
  const [isWorking, setWorking] = React.useState(false)
  const [config, setConfig] = React.useState(initialConfig)

  const prepareConfig = React.useCallback(
    async (accessToken: string, orgId: string): Promise<void> => {
      try {
        setWorking(true)

        const pluginDetes = await doShippingReqisterRequest(
          accessToken,
          orgId,
          config.metaBag,
        )
        await doConfigureRequest(
          pluginDetes.accessToken,
          orgId,
          pluginDetes.organizationName,
          pluginDetes.integrationId,
        )

        setConfig({
          ...config,
          isConfigured: true,
          details: {
            integrationId: pluginDetes.integrationId,
            organizationId: orgId,
            organizationName: pluginDetes.organizationName,
          },
        })
      } catch (error) {
        const axiosError = error as AxiosError
        console.error(axiosError)
      } finally {
        setWorking(false)
      }
    },
    [config],
  )

  const updateIsDarkLogo = React.useCallback(async () => {
    setConfig({
      ...config,
      isDarkLogo: !config.isDarkLogo,
    })

    doUpdateUseDarkRequest(!config.isDarkLogo).catch(() => {
      setConfig({
        ...config,
        isDarkLogo: !config.isDarkLogo,
      })
    })
  }, [config])

  const contextValue = React.useMemo(
    () => ({
      isWorking,
      config,
      prepareConfig,
      updateIsDarkLogo,
    }),
    [config, isWorking, prepareConfig, updateIsDarkLogo],
  )

  return (
    <ConfigContext.Provider value={contextValue}>
      {children}
    </ConfigContext.Provider>
  )
}

export default ConfigProvider
