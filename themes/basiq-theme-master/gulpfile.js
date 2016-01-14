'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');

gulp.task('styles', function() {
  gulp.src('scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./'))
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
