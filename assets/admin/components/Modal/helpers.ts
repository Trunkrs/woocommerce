// eslint-disable-next-line import/prefer-default-export
export const getContentHeightOffset = (): number => {
  const adminBar = document.getElementById('wpadminbar')
  const content = document.getElementById('wpbody-content')

  let contentOffset = 0
  if (content) {
    const padTop = window
      .getComputedStyle(content, null)
      .getPropertyValue('padding-top')
    const padBot = window
      .getComputedStyle(content, null)
      .getPropertyValue('padding-bottom')

    contentOffset = parseInt(padTop, 10) + parseInt(padBot, 10)
  }

  return (adminBar?.clientHeight ?? 32) + contentOffset
}
