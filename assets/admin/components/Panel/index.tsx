import React from 'react'
import clsx from 'clsx'

import './Panel.scss'

interface PanelProps {
  title: string
  footerContent?: React.ReactElement
  classes?: {
    root?: string
    header?: string
    content?: string
    footer?: string
  }
}

const Panel: React.FC<PanelProps> = ({
  title,
  footerContent,
  classes,
  children,
}) => {
  return (
    <div className={clsx('tr-wc-panelContainer', classes?.root)}>
      <span className={clsx('tr-wc-panelHeader', classes?.header)}>
        <p>{title}</p>
      </span>

      <span className={clsx('tr-wc-panelContent', classes?.content)}>
        {children}
      </span>

      {footerContent && (
        <span className={clsx('tr-wc-panelFooter', classes?.footer)}>
          <span>{footerContent}</span>
        </span>
      )}
    </div>
  )
}

export default Panel
