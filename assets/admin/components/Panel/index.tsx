import React from 'react'

import './Panel.scss'

interface PanelProps {
  title: string
  footerContent?: React.ReactElement
}

const Panel: React.FC<PanelProps> = ({ title, footerContent, children }) => {
  return (
    <div className="tr-wc-panelContainer">
      <span className="tr-wc-panelHeader">
        <p>{title}</p>
      </span>

      <span className="tr-wc-panelContent">{children}</span>

      {footerContent && (
        <span className="tr-wc-panelFooter">
          <span>{footerContent}</span>
        </span>
      )}
    </div>
  )
}

export default Panel
