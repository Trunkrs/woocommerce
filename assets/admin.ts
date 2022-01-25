import { render, createElement } from '@wordpress/element'

import './admin.scss'
import AdminApp from './admin/AdminApp'

// eslint-disable-next-line import/prefer-default-export
export const appElement = document.getElementById('tr-wc-settings')

if (appElement) {
  render(createElement(AdminApp), appElement)
}
