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
        <p>Platform connectie</p>
      </span>
      <span className="tr-wc-panelContent">
        <span className="tr-wc-detailsPanel-connected">
          <Check className="tr-wc-checkVector" />
          <span>
            <h3>Deze winkel is verbonden</h3>
            <p>U bent klaar om uw bestellingen met Trunkrs te verzenden.</p>
          </span>
        </span>
        <span>
          <h4>Integratie nummer:</h4>
          <p>{integrationId}</p>
          <h4>Organisatie nummer:</h4>
          <p>{organizationId}</p>
          <h4>Organisatie naam:</h4>
          <p>{organizationName}</p>
        </span>
      </span>
      <span className="tr-wc-panelFooter">
        <span>
          <Button href={manageUrl} color="white">
            <Linkout className="tr-wc-buttonVector" />
            Beheer
          </Button>
        </span>
      </span>
    </div>
  )
}

export default DetailsPanel
