import Axios from 'axios'

import constants from '../../../shared/constants'

interface IntegrationResponse {
  integrationId: string
  organizationName: string
  accessToken: string
}

export const doShippingReqisterRequest = async (
  userAccessToken: string,
  orgId: string,
  meta: { [key: string]: string },
): Promise<IntegrationResponse> => {
  const { data } = await Axios.request<IntegrationResponse>({
    method: 'POST',
    baseURL: constants.apiBaseUrl,
    url: 'integrations',
    headers: {
      Authorization: `Bearer ${userAccessToken}`,
      'X-Organization-Id': orgId,
    },
    data: {
      type: 'WooCommerce',
      name: window.location.hostname,
      version: 1,
      meta,
    },
  })

  return data
}

export const doConfigureRequest = async (
  accessToken: string,
  orgId: string,
  orgName: string,
  integrationId: string,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_register-plugin')
  request.append('accessToken', accessToken)
  request.append('organizationId', orgId)
  request.append('organizationName', orgName)
  request.append('integrationId', integrationId)

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateUseDarkRequest = async (
  isDarkLogo: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-dark')
  request.append('isDarkLogo', isDarkLogo.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}
