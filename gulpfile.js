var gulp = require( 'gulp' )
  , minifyCSS = require( 'gulp-minify-css' )
  , concat = require( 'gulp-concat' )
  , uglify  = require( 'gulp-uglify' )
  , argv = require( 'yargs' ).argv;

// minify CSS to build.css
gulp.task( 'build-css', function () {

    var cssFiles = [
        './public/css/base.css',
        './public/css/structure.css',
        './public/css/helpers.css',
        './public/css/forms.css',
        './public/css/buttons.css',
        './public/css/tables.css',
        './public/css/home.css',
        './public/css/auth.css',
        './public/css/post.css',
        './public/css/admin.css',
        './public/css/events.css',
        './public/css/spaces.css',
        './public/css/about.css',
        './public/css/pikaday.css',
        './public/css/typeahead.css',
        './public/css/timepicker.css',
        './public/css/jcrop.css',
        './public/css/modal.css',
        './public/css/overwrites.css',
        './public/css/media.css'
    ];

    var opts = {
        keepBreaks: true
    };

    gulp.src( cssFiles )
        .pipe( concat( 'build.css' ) )
        .pipe( minifyCSS( opts ) )
        .pipe( gulp.dest( './public/css/' ) );

});

// minify main app JS into main.js
gulp.task( 'build-js-main', function () {

    var jsFiles = [
        './public/js/jquery.js',
        './public/js/jquery.easing-1.3.js',
        './public/js/jquery.filmroll.js',
        './public/js/jquery.scrollTo.js',
        './public/js/pikaday.js',
        './public/js/jquery.pikaday.js',
        './public/js/moment.js',
        './public/js/underscore.js',
        './public/js/clndr.js',
        './public/js/jquery.modal.js'
        './public/js/audio.js'
    ];

    gulp.src( jsFiles )
      .pipe( concat( 'main.js' ) )
      .pipe( uglify() )
      .pipe( gulp.dest( './public/js/dist/' ) );

});

// minify admin JS into admin.js
gulp.task( 'build-js-admin', function () {

    var jsFiles = [
        './public/js/jquery.js',
        './public/js/pikaday.js',
        './public/js/jquery.pikaday.js',
        './public/js/jquery.timepicker.js',
        './public/js/jquery.typeahead.js',
        './public/js/jquery.jcrop.js',
        './public/js/jquery.scrollTo.js',
        './public/js/moment.js',
        './public/js/underscore.js',
        './public/js/clndr.js'
    ];

    gulp.src( jsFiles )
      .pipe( concat( 'admin.js' ) )
      .pipe( uglify() )
      .pipe( gulp.dest( './public/js/dist/' ) );

});