import React from 'react'

interface ShippingMethodProps {
  price?: string
}

const ShippingMethod: React.FC<ShippingMethodProps> = ({ price }) => {
  return <p>Trunnekers: &euro; {price}</p>
}

export default ShippingMethod
