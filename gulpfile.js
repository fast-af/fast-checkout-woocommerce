const { series, src, dest } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const eslint = require('gulp-eslint');
const sass = require('gulp-sass')(require('sass'));
const jsAdminPath = 'assets/src/js/admin/**/*.js';
const jsFrontendPath = 'assets/src/js/frontend/**/*.js';
const sassPath = 'assets/src/styles/**/*.scss';

function concatAdminScripts() {
	return src(jsAdminPath)
		.pipe(concat('scripts.min.js'))
		.pipe(uglify())
		.pipe(dest('assets/dist/admin'));
}

function lintAdminScripts() {
	return src(jsAdminPath)
		.pipe(eslint())
		.pipe(eslint.format());
}

function concatFrontendScripts() {
	return src(jsFrontendPath)
		.pipe(concat('scripts.min.js'))
		.pipe(uglify())
		.pipe(dest('assets/dist/frontend'));
}

function lintFrontendScripts() {
	return src(jsFrontendPath)
		.pipe(eslint())
		.pipe(eslint.format());
}

function buildStyles() {
	return src(sassPath)
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(dest('assets/dist'));
}

exports.concatadmin = concatAdminScripts;
exports.lintadmin = lintAdminScripts;
exports.concatfrontend = concatFrontendScripts;
exports.lintfrontend = lintFrontendScripts;
exports.styles = buildStyles;
exports.default = series(lintAdminScripts, concatAdminScripts, lintFrontendScripts, concatFrontendScripts, buildStyles);
