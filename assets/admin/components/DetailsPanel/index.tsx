import React from 'react'

import constants from '../../../shared/constants'

import Check from '../vectors/Check'
import Button from '../Button'

import './DetailsPanel.scss'
import Linkout from '../vectors/Linkout'

interface DetailsPanelProps {
  integrationId: string
  organizationId: string
  organizationName: string
}

const DetailsPanel: React.FC<DetailsPanelProps> = ({
  integrationId,
  organizationId,
  organizationName,
}) => {
  const manageUrl = `${constants.portalBaseUrl}/${organizationId}/settings/integrations/${integrationId}`

  return (
    <div className="tr-wc-detailsPanel">
      <span className="tr-wc-panelHeader">
        <p>Platform connection</p>
      </span>
      <span className="tr-wc-panelContent">
        <span className="tr-wc-detailsPanel-connected">
          <Check className="tr-wc-checkVector" />
          <span>
            <h3>Your store is connected</h3>
            <p>You are ready to ship your orders with Trunkrs.</p>
          </span>
        </span>
        <span>
          <h4>Integration identifier:</h4>
          <p>{integrationId}</p>
          <h4>Organization identifier:</h4>
          <p>{organizationId}</p>
          <h4>Organization name:</h4>
          <p>{organizationName}</p>
        </span>
      </span>
      <span className="tr-wc-panelFooter">
        <span>
          <Button href={manageUrl} color="white">
            <Linkout className="tr-wc-buttonVector" />
            Manage
          </Button>
        </span>
      </span>
    </div>
  )
}

export default DetailsPanel
