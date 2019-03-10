var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/_encore')
    .setPublicPath('/_encore')

    .cleanupOutputBeforeBuild()
    .enableSingleRuntimeChunk()

    .addEntry('common_admin', './assets/CommonBundle/admin/admin.js')
    .addEntry('cudi_prof', './assets/CudiBundle/prof/prof.js')
    .addEntry('cudi_sale', './assets/CudiBundle/sale/sale.js')

    .addLoader({
        test: require.resolve('jquery'),
        use: [{
            loader: 'expose-loader',
            options: 'jQuery'
        }, {
            loader: 'expose-loader',
            options: '$'
        }]
    })

    .enableSassLoader()
    .enablePostCssLoader()

    .enableSingleRuntimeChunk()

    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

module.exports = Encore.getWebpackConfig();
