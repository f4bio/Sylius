const path = require('path');
const Encore = require('@symfony/webpack-encore');

const shopResources = path.resolve(__dirname, 'src/Sylius/Bundle/ShopBundle/Resources/private/img');
const adminResources = path.resolve(__dirname, 'src/Sylius/Bundle/AdminBundle/Resources/private/img');
const uiBundleResources = path.resolve(__dirname, 'src/Sylius/Bundle/UiBundle/Resources/private/');

// Shop config
Encore
  .setOutputPath('public/build/shop/')
  .setPublicPath('/build/shop')
  .disableSingleRuntimeChunk()
  .copyFiles({
    from: shopResources,
    to: 'images/[name].[hash:8].[ext]',
  })
  .copyFiles({
    from: path.join(uiBundleResources, 'img'),
    to: 'images/[name].[hash:8].[ext]',
  })
  .addStyleEntry('shop-entry', './src/Sylius/Bundle/ShopBundle/Resources/private/scss/bundle.scss')
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader();

const shopConfig = Encore.getWebpackConfig();

shopConfig.resolve.alias['sylius/ui-resources'] = uiBundleResources;
shopConfig.name = 'shop';

Encore.reset();

// Admin config
Encore
  .setOutputPath('public/build/admin/')
  .setPublicPath('/build/admin')
  .disableSingleRuntimeChunk()
  .copyFiles({
    from: adminResources,
    to: 'images/[name].[hash:8].[ext]',
  })
  .copyFiles({
    from: path.join(uiBundleResources, 'img'),
    to: 'images/[name].[hash:8].[ext]',
  })
  .addStyleEntry('admin-entry', './src/Sylius/Bundle/AdminBundle/Resources/private/sass/bundle.scss')
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader();

const adminConfig = Encore.getWebpackConfig();

adminConfig.resolve.alias['sylius/ui-resources'] = uiBundleResources;
adminConfig.externals = Object.assign({}, adminConfig.externals, { window: 'window', document: 'document' });
adminConfig.name = 'admin';

module.exports = [shopConfig, adminConfig];
