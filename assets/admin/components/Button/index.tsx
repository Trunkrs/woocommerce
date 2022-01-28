import React from 'react'
import clsx from 'clsx'

import './Button.scss'

interface ButtonProps {
  type?: 'submit' | 'button' | 'reset'
  color: 'green' | 'blue' | 'white'
  size?: 'normal' | 'small'
  href?: string
  disabled?: boolean
  className?: string
  onClick?: () => void | Promise<void>
}

const Button: React.FC<ButtonProps> = ({
  type = 'button',
  color,
  size,
  href,
  disabled,
  className,
  children,
  onClick,
}) => {
  const classes = clsx(
    'tr-wc-button',
    {
      'tr-wc-blue': color === 'blue',
      'tr-wc-green': color === 'green',
      'tr-wc-white': color === 'white',
      'tr-wc-button-small': size === 'small',
    },
    className,
  )

  const element = !href ? (
    // eslint-disable-next-line jsx-a11y/control-has-associated-label,react/button-has-type
    <button type={type} onClick={onClick} />
  ) : (
    // eslint-disable-next-line jsx-a11y/anchor-has-content,jsx-a11y/control-has-associated-label
    <a href={href} target="_blank" rel="noreferrer noopener nofollow" />
  )

  return React.cloneElement(
    element,
    {
      className: classes,
      disabled,
    },
    children,
  )
}

export default Button
