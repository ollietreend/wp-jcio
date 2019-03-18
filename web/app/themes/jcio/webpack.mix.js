const mix = require('laravel-mix');

mix.setPublicPath('./dist/');

mix.js('src/scripts/main.js', 'js')
  .sass('src/styles/main.scss', 'css/main.css')
  .sass('src/styles/editor-style.scss', 'css/editor-style.css')
  .copy('src/images/*.{jpg,jpeg,png,gif,svg,ico}', 'dist/images/');

if (mix.inProduction()) {
  mix.version();
} else {
  mix.sourceMaps();
}
