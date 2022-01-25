import React from 'react'
import clsx from 'clsx'

import './IconButton.scss'

interface IconButtonProps {
  icon: React.ReactElement
  className?: string
  onClick?: () => void | Promise<void>
}

const IconButton: React.FC<IconButtonProps> = ({
  icon,
  className,
  onClick,
}) => (
  <button
    type="button"
    className={clsx('tr-wc-iconButton-root', className)}
    onClick={onClick}
  >
    <icon.type
      {...icon.props}
      className={clsx('tr-wc-iconButton-icon', icon.props.className)}
    />
  </button>
)

export default IconButton
