import React from 'react'
import clsx from 'clsx'
import EventListener from 'react-event-listener'

import Portal from '../Portal'
import IconButton from '../IconButton'
import Close from '../vectors/Close'

import Backdrop from './Backdrop'
import './Modal.scss'

interface ModalProps {
  open: boolean
  classes?: {
    root?: string
    backdrop?: string
    container?: string
    header?: string
    contentPanel?: string
    content?: string
    footer?: string
  }
  title?: string
  footerContent?: React.ReactElement
  onClose?: () => void
}

const Modal: React.FC<ModalProps> = ({
  open,
  classes,
  title,
  footerContent,
  children,
  onClose,
}) => {
  const rootRef = React.useRef<HTMLDivElement | null>(null)
  const containerRef = React.useRef<HTMLDivElement | null>(null)

  const handleReCalculation = React.useCallback(() => {
    const { current: rootEl } = rootRef
    const { current: containerEl } = containerRef

    if (!containerEl || !rootEl) return

    const { clientHeight, clientWidth } = containerEl

    rootEl.setAttribute(
      'style',
      `top: calc(50vh - ${Math.round(
        clientHeight / 2,
      )}px);left: calc(50vw - ${Math.round(
        clientWidth / 2,
      )}px); width: ${clientWidth}px;height: ${clientHeight}px;`,
    )
  }, [])

  React.useEffect(handleReCalculation, [handleReCalculation, open])

  return open ? (
    <>
      <Backdrop open className={classes?.backdrop} onClick={onClose} />

      <EventListener target="window" onResize={handleReCalculation} />

      <Portal>
        <div className={clsx('tr-wc-modal-root', classes?.root)} ref={rootRef}>
          <div
            role="dialog"
            className={clsx('tr-wc-modal-rootContainer', classes?.container)}
            ref={containerRef}
          >
            <div
              className={clsx(
                'tr-wc-modal-contentContainer',
                classes?.contentPanel,
              )}
            >
              <span
                role="toolbar"
                className={clsx('tr-wc-modal-header', classes?.header)}
              >
                {title ? <h3>{title}</h3> : <span />}
                <IconButton icon={<Close />} onClick={onClose} />
              </span>

              <span className={clsx('tr-wc-modal-content', classes?.content)}>
                {children}
              </span>
            </div>

            {footerContent && (
              <span
                role="toolbar"
                className={clsx('tr-wc-modal-footer', classes?.footer)}
              >
                {footerContent}
              </span>
            )}
          </div>
        </div>
      </Portal>
    </>
  ) : null
}

export default Modal
