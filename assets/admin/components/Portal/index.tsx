import React from 'react'
import ReactDOM from 'react-dom'

type PortalRefFunction = (node: HTMLDivElement | null) => void

interface PortalProps {
  portalRef?: PortalRefFunction | React.MutableRefObject<HTMLDivElement | null>
}

const portalRoot = document.getElementById('portal-root')

class Portal extends React.PureComponent<PortalProps> {
  private readonly portalEl: HTMLDivElement

  public constructor(props: PortalProps) {
    super(props)

    this.portalEl = document.createElement('div')
  }

  public componentDidMount(): void {
    portalRoot?.appendChild(this.portalEl)
    this.updateRef(this.portalEl)
  }

  public componentWillUnmount(): void {
    portalRoot?.removeChild(this.portalEl)
    this.updateRef(null)
  }

  private updateRef = (node: HTMLDivElement | null): void => {
    const { portalRef } = this.props

    if (!portalRef) {
      return
    }

    if (typeof portalRef === 'function') {
      portalRef(node)
    } else {
      portalRef.current = node
    }
  }

  public render() {
    const { children } = this.props

    return ReactDOM.createPortal(children, this.portalEl)
  }
}

export default Portal
