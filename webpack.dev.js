const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const ESLintPlugin = require('eslint-webpack-plugin');

module.exports = merge(common, {
  devServer: {
    port: 3000,
    static: false,
    https: true,
    compress: true,
    host: '0.0.0.0',
    static: "public/assets",
    hot: true,
    liveReload: true,
    allowedHosts: 'all',
    proxy: {
      path: '/',
      target: 'https://caddy:443',
      disableHostCheck: true,
      changeOrigin: true,
      secure: true,
      onProxyRes: function (proxyRes, req, res)
      {
        if (proxyRes.headers &&
          proxyRes.headers['content-type'] &&
          proxyRes.headers['content-type'].match('text/html|application/json'))
        {

          var _write = res.write, _writeHead = res.writeHead;

          res.writeHead = function ()
          {
            if (proxyRes.headers && proxyRes.headers['content-length'])
            {
              res.removeHeader('content-length');
            }

            // This disables chunked encoding
            res.setHeader('transfer-encoding', '');

            // Disable cache for all http as well
            res.setHeader('cache-control', 'no-cache');

            _writeHead.apply(this, arguments);
          };

          res.write = function (data)
          {
            //set again the domain of the wordpress site to replace js, css, images paths to the webpack dev server
            const newdata = data.toString().replace(/wp\.localhost/g, req.headers.host);
            _write.call(res, newdata);
          }
        }
      }
    },
    client: {
      progress: true,
      overlay: true
    },
    devMiddleware: {
      index: true,
      mimeTypes: { phtml: 'text/html' },
      publicPath: './public/assets/',
      serverSideRender: false,
      writeToDisk: true,
    },
    onListening: function (devServer)
    {
      if (!devServer)
      {
        throw new Error('webpack-dev-server is not defined');
      }

      const port = devServer.server.address().port;
      console.log('Listening on port:', port);
    },
  },
  devtool: "source-map",
  plugins: [
    new ESLintPlugin(),
  ],
});
