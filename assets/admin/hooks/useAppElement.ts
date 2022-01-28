const appElement = document.getElementById('tr-wc-settings')

const features = {
  element: appElement as HTMLDivElement,
  setStyle: (cssString: string) => {
    appElement?.setAttribute('style', cssString)
  },
}

const useAppElement = () => features

export default useAppElement
