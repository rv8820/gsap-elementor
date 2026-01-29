const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';

  return {
    entry: {
      'gsap-frontend': './src/js/frontend.js',
      'gsap-editor': './src/js/editor.js',
    },
    output: {
      path: path.resolve(__dirname, 'assets/js'),
      filename: '[name].js',
      clean: false,
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                ['@babel/preset-env', { targets: '> 1%, not dead' }],
              ],
            },
          },
        },
        {
          test: /\.css$/,
          use: [MiniCssExtractPlugin.loader, 'css-loader'],
        },
      ],
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: '../css/[name].css',
      }),
    ],
    optimization: {
      minimizer: [
        new TerserPlugin({ extractComments: false }),
        new CssMinimizerPlugin(),
      ],
    },
    devtool: isProduction ? false : 'source-map',
    resolve: {
      extensions: ['.js'],
    },
  };
};
