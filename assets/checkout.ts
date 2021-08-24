import { render, createElement } from '@wordpress/element'
import ShippingMethod from './checkout/ShippingMethod'

import './checkout.scss'

const element = document.getElementById('trunkrs-woocommerce__')

if (element) {
  const { price } = element.dataset
  render(createElement(ShippingMethod, { price }), element.parentElement)
}
