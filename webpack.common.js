const path = require("path");
const webpack = require("webpack");
const WebpackMessages = require('webpack-messages');
const WebpackBar = require('webpackbar');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000,
    },
    resolve: {
        fallback: {
            "fs": false
        },
    },
    entry: {
        dashboard: [
            "/src/js/dashboard/app.js",
        ],
        landing: [
            "/src/js/landing/app.js",
        ],
        app: [
            "/src/scss/app.scss"
        ]
    },
    output: {
        path: path.join(__dirname, "public/assets"),
        filename: 'js/[name].min.js',
    },
    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env"],
                    },
                },
            },
            {
                test: /\.s?css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            },
        ],
    },
    optimization: {
        splitChunks: {
            chunks: 'async',
            minSize: 20000,
            minRemainingSize: 0,
            minChunks: 1,
            maxAsyncRequests: 30,
            maxInitialRequests: 30,
            enforceSizeThreshold: 50000,
            cacheGroups: {
                defaultVendors: {
                    test: /[\\/]node_modules[\\/]/,
                    priority: -10,
                    reuseExistingChunk: true,
                },
                default: {
                    minChunks: 2,
                    priority: -20,
                    reuseExistingChunk: true,
                },
            },
        },
        concatenateModules: true,
        emitOnErrors: true,
    },
    plugins: [
        new webpack.BannerPlugin(`Copyright 2022 NiWee Productions.`),
        new WebpackMessages({
            name: 'Trident',
            logger: str => console.log(`>> ${str}`)
        }),
        new WebpackBar({
            name: "Trident",
            color: "#006994",
            basic: false,
            profile: true,
            fancy: true,
            reporters: [
                'fancy',
            ],
        }),
        new MiniCssExtractPlugin({
            filename: "css/[name].min.css"
        }),
    ],
};
