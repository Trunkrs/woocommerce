const { DefinePlugin } = require('webpack')

const defaultConfig = require('@wordpress/scripts/config/webpack.config')
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin')
const CopyPlugin = require("copy-webpack-plugin")

const allowedPrefixes = ['AUTH0', 'TRUNKRS']
const definePublicEnvironment = () => {
  const variables = Object.keys(process.env)

  const allowedVars = variables.reduce(
    (vars, currentVar) => {
      const isAllowed = allowedPrefixes.some(prefix => currentVar.startsWith(prefix))
      if (isAllowed) {
        vars[currentVar] = process.env[currentVar]
      }

      return vars
    },
    { NODE_ENV: process.env.NODE_ENV },
  )

  return JSON.stringify(allowedVars)
}

module.exports = {
  ...defaultConfig,

  entry: {
    admin: './assets/admin.ts',
    checkout: './assets/checkout.ts',
  },

  externals: {
    'react': 'React',
    'react-dom': 'ReactDOM',
  },

  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.tsx?$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
    ]
  },

  resolve: {
    ...defaultConfig.resolve,
    extensions: [
      ".tsx",
      ".ts",
      ".js",
      ".jsx",
      ".svg",
    ],
  },

  plugins: [
    ...defaultConfig.plugins.filter(
      (plugin) =>
        plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
    ),
    new DefinePlugin({
      'process.env': definePublicEnvironment(),
    }),
    new WooCommerceDependencyExtractionWebpackPlugin(),
    new CopyPlugin({
      patterns: [
        {
          from: "assets/icons/**",
          to: "icons/[name].[ext]",
        },
      ],
    })
  ],
}
