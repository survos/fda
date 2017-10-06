// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore.enableVersioning(false);

var
    jsFiles = [
        "./assets/js/main.js"
    ],
    cssFiles = [
        "./assets/css/global.scss",
//        'font-awesome/scss/font-awesome.scss',
//        'bootstrap-sass/assets/stylesheets/_bootstrap.scss',
        'leaflet/dist/leaflet.css',
//        './src/Posse/WebBundle/Resources/assets/scss/main.scss'
    ];

Encore

// directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath('/build')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // will output as web/build/global.css
    .addStyleEntry('global', './assets/css/global.scss')

    .addStyleEntry('style', cssFiles)

    .addEntry('app', jsFiles)



    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
