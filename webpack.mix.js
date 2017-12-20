let mix = require('laravel-mix');

const moduleDir = __dirname;
const resPath = moduleDir + '/Resources/assets';
const compileTo = moduleDir + '/Assets';

mix.setPublicPath('.')
mix
    .js(resPath + '/js/admin/index.js', compileTo + '/admin/js/index.js')
    .disableNotifications();