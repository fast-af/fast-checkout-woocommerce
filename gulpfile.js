const { series, src, dest } = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const eslint = require('gulp-eslint');
const jsPath = 'assets/src/js/**/*.js';

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

exports.concat = concatScripts;
exports.lint = lintScripts;
exports.default = series(lintScripts, concatScripts);
