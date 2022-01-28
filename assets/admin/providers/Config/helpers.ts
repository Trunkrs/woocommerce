import Axios from 'axios'

import constants from '../../../shared/constants'

interface IntegrationResponse {
  integrationId: string
  organizationName: string
  accessToken: string
}

export interface AuditLogEntry {
  orderId: string
  timestamp: string
  entries: [
    {
      fieldName: string
      fieldValue: string
      comparisons: [
        {
          operator: string
          compareValue: string
          result: boolean
        },
      ]
    },
  ]
}

export const decodeHtmlString = (htmlEncoded: string): string => {
  const txt = document.createElement('textarea')
  txt.innerHTML = htmlEncoded

  return txt.value
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

export const doUpdateUseTntLinksRequest = async (
  isDarkLogo: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-tnt-links')
  request.append('isEmailLinksEnabled', isDarkLogo.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateUseTntAccountsRequest = async (
  isDarkLogo: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-tnt-account')
  request.append('isAccountTrackTraceEnabled', isDarkLogo.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateUseAllOrdersAreTrunkrsRequest = async (
  isDarkLogo: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-all-orders-are-trunkrs')
  request.append('isAllOrdersAreTrunkrsEnabled', isDarkLogo.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateUseBigTextRequest = async (
  isEnabled: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-big-checkout-text')
  request.append('isUseBigTextEnabled', isEnabled.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateSubRenewalsEnabled = async (
  isEnabled: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-sub-renewals')
  request.append('isSubRenewalsEnabled', isEnabled.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateOrderRulesEnabled = async (
  isEnabled: boolean,
): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-use-order-rules')
  request.append('isOrderRulesEnabled', isEnabled.toString())

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const doUpdateOrderRules = async (orderRules: string): Promise<void> => {
  const request = new FormData()
  request.append('action', 'tr-wc_update-order-rules')
  request.append('orderRules', orderRules)

  await Axios.request({
    method: 'POST',
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    url: ajaxurl,
    data: request,
  })
}

export const findAuditLogs = async (): Promise<AuditLogEntry[]> => {
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  const { data } = await Axios.get<AuditLogEntry[]>(`${ajaxurl}`, {
    params: { action: 'tr-wc_get-order-logs' },
  })

  return data
}
