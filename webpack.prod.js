const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");

module.exports = merge(common, {
    output: {
        clean: true,
    },
    devtool: "hidden-nosources-source-map",
});
