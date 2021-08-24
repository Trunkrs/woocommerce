import React from 'react'

export interface Configuration {
  isConfigured: boolean
  isDarkLogo: boolean
  details: {
    integrationId: string
    organizationId: string
    organizationName: string
  }
  metaBag: { [key: string]: string }
}

export type ConfigContext = {
  isWorking: boolean
  config: Configuration | null
  prepareConfig: (accessToken: string, orgId: string) => Promise<void>
  updateIsDarkLogo: () => Promise<void>
}

const ConfigContext = React.createContext<ConfigContext>({
  isWorking: false,
  config: null,
  prepareConfig: () => {
    throw new Error('Not implemented!')
  },
  updateIsDarkLogo: () => {
    throw new Error('Not implemented!')
  },
})

export default ConfigContext
