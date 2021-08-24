import { render, createElement } from '@wordpress/element'

import './admin.scss'
import AdminApp from './admin/AdminApp'

const element = document.getElementById('tr-wc-settings')
if (element) {
  render(createElement(AdminApp), element)
}
