'use strict';

var gulp = require('gulp');
var postcss = require('gulp-postcss');
var sass = require('gulp-sass');

var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');

var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');


gulp.task('styles', function() {
  var processors = [
       autoprefixer({ browsers: ['last 2 versions'] }),
       cssnano
   ];

    return gulp.src('scss/**/*.scss')
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(postcss(processors))
      .pipe(sourcemaps.write())
      .pipe(gulp.dest('./'))
      .pipe(livereload());
});

//Watch task
gulp.task('default',function() {
  gulp.watch('scss/**/*.scss', ['styles']);
  gulp.watch('templates/**/*.twig');
  livereload.listen();
});
