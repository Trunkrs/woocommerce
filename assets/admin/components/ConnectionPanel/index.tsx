import React from 'react'

import ConnectButton, { LoginResult } from '../ConnectButton'

import './ConnectionPanel.scss'

interface ConnectionPanelProps {
  loading?: boolean
  onLoginDone?: (result: LoginResult) => void | Promise<void>
}

const ConnectionPanel: React.FC<ConnectionPanelProps> = ({
  loading,
  onLoginDone,
}) => (
  <div className="tr-wc-connectionPanel">
    <span className="tr-wc-panelHeader">
      <p>Platform connection</p>
    </span>
    <span className="tr-wc-panelContent">
      <span className="tr-wc-connectionStatus">
        <h3>Not connected to Trunkrs</h3>
        <p>Connect your store to the Trunkrs platform to start shipping.</p>
      </span>

      <span>
        <ConnectButton loading={loading} onLoginDone={onLoginDone} />
      </span>
    </span>
  </div>
)

export default ConnectionPanel
