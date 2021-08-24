import React from 'react'

import './CircularProgress.scss'

interface CircularProgressProps {
  size?: number
  thickness?: number
}

const CircularProgress: React.FC<CircularProgressProps> = ({
  size = 20,
  thickness = 2,
}) => (
  <span>
    {React.createElement(
      'style',
      {},
      `
     .tr-wc-progress-circular {
       height: ${size}px;
       width: ${size}px;
     }

     .tr-wc-progress-circular:indeterminate::before,
     .tr-wc-progress-circular:indeterminate::-webkit-progress-value {
       border-width: ${thickness}px;
     }
    `,
    )}
    <progress className="tr-wc-progress-circular" />
  </span>
)

export default CircularProgress
