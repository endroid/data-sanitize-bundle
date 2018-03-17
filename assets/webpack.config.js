let Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('../src/Resources/public/build/')
    .setPublicPath('/bundles/endroiddatasanitize/build')
    .setManifestKeyPrefix('/build')
    .cleanupOutputBeforeBuild()
    .createSharedEntry('base', './js/base.js')
    .addEntry('merger', './js/merger.js')
    .autoProvidejQuery()
    .enableVueLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();