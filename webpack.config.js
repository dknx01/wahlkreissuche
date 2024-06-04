var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    // .addEntry('mapLeaflet', './assets/js/leaflet/map.js')
    // .addEntry('mapLeafletLayer', './assets/js/leaflet/map_layer.js')
    // .addEntry('mapLeafletLayerState', './assets/js/leaflet/map_layer_states.js')
    // .addEntry('wishMapLeafletLayerState', './assets/js/leaflet/wish_map_layer_states.js')
    .addEntry('app', './assets/js/app.js')
    .addEntry('location', './assets/js/plakate/AddLocation.js')
    .addEntry('address', './assets/js/plakate/Address.js')
    .addEntry('addressGps', './assets/js/plakate/AddLocationGps.js')
    .addEntry('wishAddressGps', './assets/js/plakate/WishAddLocationGps.js')
    .addEntry('manualAddress', './assets/js/plakate/AddLocationManual.js')
    .addEntry('manualAddressWish', './assets/js/plakate/WishAddLocationManual.js')
    // .addEntry('agh_kreise', './assets/js/leaflet/agh_kreise.js')
    // .addEntry('btw_kreise', './assets/js/leaflet/btw_kreise.js')
    .addEntry('search_results', './assets/js/leaflet/map_search_results.js')
    .addEntry('user_overview', './assets/js/user_overview.js')
    .addEntry('election_poster_overview', './assets/js/election_poster_overview.js')
    .addEntry('election_poster_edit', './assets/js/plakate/editPoster.js')
    .addEntry('wahllokaltour_map', './assets/js/leaflet/wahllokaltour/map_wahllokaltour.js')
    .addEntry('wahllokaltour_points', './assets/js/leaflet/wahllokaltour/wahllokaltour_points.js')
    // .addEntry('map', './assets/js/map.js')
    // .addEntry('map2', './assets/js/map.ts')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')
    .addEntry('stimulus_app', './assets/app.js')
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .autoProvideVariables(
        {
            $: 'jquery',
            jQuery: 'jquery',
        }
    )

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
    .copyFiles(
        {
            'from': './assets/images'
        },
    )
;

module.exports = Encore.getWebpackConfig();
