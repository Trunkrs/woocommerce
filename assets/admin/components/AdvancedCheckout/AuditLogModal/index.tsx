import React from 'react'

import { AuditLogEntry, findAuditLogs } from '../../../providers/Config/helpers'

import CircularProgress from '../../CircularProgress'
import Modal from '../../Modal'

import LogEntry from './LogEntry'

import './AuditLogModal.scss'

interface AuditLogModalProps {
  open: boolean
  onClose: () => void
}

const AuditLogModal: React.FC<AuditLogModalProps> = ({ open, onClose }) => {
  const [isLoading, setLoading] = React.useState(true)
  const [logs, setLogs] = React.useState<AuditLogEntry[]>([])

  React.useEffect(() => {
    if (!open) return

    setLoading(true)

    findAuditLogs()
      .then(setLogs)
      .finally(() => setLoading(false))
  }, [open])

  return (
    <Modal
      open={open}
      classes={{
        container: 'tr-wc-auditLogModal-container',
        contentPanel: 'tr-wc-auditLogModal-contentPanel',
        content: 'tr-wc-auditLogModal-panel',
      }}
      title="Selectie logboek"
      onClose={onClose}
    >
      {!isLoading ? (
        logs.map((entry) => <LogEntry key={entry.timestamp} {...entry} />)
      ) : (
        <div className="tr-wc-auditLogModal-loading">
          <CircularProgress size={48} thickness={4} />
        </div>
      )}
    </Modal>
  )
}

export default AuditLogModal
