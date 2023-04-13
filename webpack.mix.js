const mix = require('laravel-mix');

// Bootstrap
mix.copy('resources/libs/bootstrap-5.3.0-grid/css/bootstrap-grid.min.css', 'public/css/bootstrap.min.css');

// Font Awesome
mix.copy('resources/libs/fontawesome-pro-6.2.0/css/solid.min.css', 'public/css/fontawesome-type.min.css');
mix.copy('resources/libs/fontawesome-pro-6.2.0/css/fontawesome.min.css', 'public/css/fontawesome.min.css');
mix.copy('resources/libs/fontawesome-pro-6.2.0/js/fontawesome.min.js', 'public/js/fontawesome.min.js');
mix.copyDirectory('resources/libs/fontawesome-pro-6.2.0/webfonts', 'public/webfonts');

// Base Style
mix.sass('resources/css/app.scss', 'public/css/app.css');
mix.copy('resources/js/app.js', 'public/js/app.js');

// Fonts
mix.copyDirectory('resources/fonts', 'public/fonts');

// Flow
mix.minify('resources/libs/flow/flow.js', 'public/js/flow.js');

// IMG
mix.copyDirectory('resources/img', 'public/img');