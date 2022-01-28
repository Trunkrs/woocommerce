/* eslint-disable jsx-a11y/no-noninteractive-element-interactions,jsx-a11y/click-events-have-key-events */
import React from 'react'
import clsx from 'clsx'

import './Backdrop.scss'

interface BackdropProps {
  open: boolean
  className?: string
  onClick?: () => void | Promise<void>
}

const Backdrop: React.FC<BackdropProps> = ({
  open,
  className,
  children,
  onClick,
}) => (
  <div
    role="banner"
    className={clsx('tr-wc-backdrop-root', className, {
      'tr-wc-backdrop-open': open,
    })}
    onClick={onClick}
  >
    <span className="tr-wc-backdrop-content">{children}</span>
  </div>
)

export default Backdrop
