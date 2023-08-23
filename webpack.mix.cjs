const mix = require('laravel-mix');

mix.js('resources/js/app/src/App.js', 'public/js')
    .css('resources/css/App.css', 'public/css');
