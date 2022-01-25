import React from 'react'

import ConnectorLineDown from '../../../vectors/ConnectorLineDown'

import './EmptyView.scss'
import AddFilter from '../../../vectors/AddFilter'

const EmptyView: React.FC = () => (
  <div className="tr-wc-rulesModal-emptyView-panel">
    <ConnectorLineDown className="tr-wc-rulesModal-emptyView-line" />
    <p className="tr-wc-rulesModal-emptyView-text">
      <AddFilter className="tr-wc-buttonVector" />
      Voeg een regel toe
    </p>
  </div>
)

export default EmptyView
