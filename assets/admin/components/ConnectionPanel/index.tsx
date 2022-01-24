import React from 'react'

import ConnectButton, { LoginResult } from '../ConnectButton'
import Panel from '../Panel'

import './ConnectionPanel.scss'

interface ConnectionPanelProps {
  loading?: boolean
  onLoginDone?: (result: LoginResult) => void | Promise<void>
}

const ConnectionPanel: React.FC<ConnectionPanelProps> = ({
  loading,
  onLoginDone,
}) => (
  <Panel title="platform connectie">
    <span className="tr-wc-connectionStatus">
      <h3>Niet verbonden met Trunkrs</h3>
      <p>
        Verbind uw winkel met het Trunkrs platform om te beginnen met verzenden.
      </p>
    </span>

    <span>
      <ConnectButton loading={loading} onLoginDone={onLoginDone} />
    </span>
  </Panel>
)

export default ConnectionPanel
