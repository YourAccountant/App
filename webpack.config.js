const path = require('path')
const MiniCssExtractPlugin = require("mini-css-extract-plugin")

module.exports = {
    entry: "./public/js/bundle.js",
    output: {
        path: path.resolve(__dirname, 'public/dist'),
        filename: "bundle.dist.js"
    },
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.html|\.php$/,
                use: [
                    {
                        loader: 'html-loader',
                        options: {
                            minimize: true
                        }
                    }
                ]
            }
        ]
    },
     plugins: [
        new MiniCssExtractPlugin({
            filename: "bundle.dist.css",
            chunkFilename: "[id].css"
        })
    ]
}
