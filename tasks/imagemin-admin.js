module.exports = function (gulp, plugins, config) {
    gulp.task('imagemin-admin', function() {
        return gulp.src(config.adminImageSrcFolder + "/**/*.{jpg,png,gif}")
            .pipe(plugins.plumber())
			.pipe(plugins.imagemin([
				plugins.imagemin.gifsicle({interlaced: true}),
				plugins.imagemin.mozjpeg({quality: 75, progressive: true}),
				plugins.imagemin.optipng({optimizationLevel: 5}),
				plugins.imagemin.svgo({
					plugins: [
						{removeViewBox: true},
						{cleanupIDs: false}
					]
				})
			]))
            .pipe(gulp.dest(config.adminImageDestFolder))
    });
}
