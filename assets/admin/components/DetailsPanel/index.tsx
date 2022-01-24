import React from 'react'

import constants from '../../../shared/constants'

import Check from '../vectors/Check'
import Linkout from '../vectors/Linkout'

import Button from '../Button'
import Panel from '../Panel'

import './DetailsPanel.scss'

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
    <Panel
      title="Platform connectie"
      footerContent={
        <Button href={manageUrl} color="white">
          <Linkout className="tr-wc-buttonVector" />
          Beheer
        </Button>
      }
    >
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
    </Panel>
  )
}

export default DetailsPanel
