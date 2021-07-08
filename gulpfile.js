const { series, src, dest } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const eslint = require('gulp-eslint');
const sass = require('gulp-sass')(require('sass'));
const jsPath = 'assets/src/js/**/*.js';
const sassPath = 'assets/src/styles/**/*.scss';

function concatScripts() {
	return src(jsPath)
		.pipe(concat('scripts.min.js'))
		.pipe(uglify())
		.pipe(dest('assets/dist'));
}

function lintScripts() {
	return src(jsPath)
		.pipe(eslint())
		.pipe(eslint.format());
}

function buildStyles() {
	return src(sassPath)
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(dest('assets/dist'));
}

exports.concat = concatScripts;
exports.lint = lintScripts;
exports.styles = buildStyles;
exports.default = series(lintScripts, concatScripts, buildStyles);
