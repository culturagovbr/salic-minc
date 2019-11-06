'use strict'
process.env.NODE_ENV = 'watch'
const webpack = require('webpack')
const config = require('../config')
const merge = require('webpack-merge')
const path = require('path')
const baseWebpackConfig = require('./webpack.base.conf')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
var BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const watchWebpackConfig = merge(baseWebpackConfig, {
    mode: 'development',
    watch: true,
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    chunks: "initial",
                    name: "manifest",
                    minChunks: 2,
                    maxInitialRequests: 5, // The default limit is too small to showcase the effect
                    minSize: 0 // This is example is too small to create commons chunks
                },
                vendor: {
                    test: /node_modules/,
                    chunks: "all",
                    name: "vendor",
                    priority: 10,
                    enforce: true
                }
            }
        },
        namedModules: true,
        namedChunks: true,
    },
    module: {
        rules: [
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    'style-loader',
                    'css-loader',
                    'postcss-loader',
                    'sass-loader',
                ],
            }
        ]
    },
    // cheap-module-eval-source-map is faster for development
    devtool: config.dev.devtool,
    plugins: [
        new webpack.EnvironmentPlugin([
            'API',
        ]),
        new webpack.DefinePlugin({
            'test': require('../config/test.env'),
        }),
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // both options are optional
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        }),
        new CopyWebpackPlugin([
            {
                from: path.resolve(__dirname, '../static'),
                to: config.dev.assetsSubDirectory,
                ignore: ['.*']
            }
        ]),
        new HtmlWebpackPlugin({
            template: 'index.html',
            filename: config.build.index,
            inject: true,
            hash: true,
            chunks: ['main', 'vendor','manifest'],
            chunksSortMode: 'dependency',
        }),
        new BrowserSyncPlugin({
            // browse to http://localhost:3000/ during development,
            // ./public directory is being served
            host: 'localhost',
            port: 3000,
            proxy: config.dev.host,
            // files: [path.resolve(__dirname, '../../application/modules/*.phtml'), path.resolve(__dirname, '../../application/modules/*.php')],

            // server: { baseDir: [config.dev.assetsSubDirectory] }
        })
    ],
})

module.exports = watchWebpackConfig
