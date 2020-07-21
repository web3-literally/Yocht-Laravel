var gulp = require('gulp');

var less = require('gulp-less');
var sourcemaps = require('gulp-sourcemaps');
var minify = require('gulp-csso');

var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var imagemin = require('gulp-imagemin');

var plumber = require('gulp-plumber');

let notify = null;
if (process.env.APP_ENV != 'development' && process.env.APP_ENV != 'production') {
    notify = require('gulp-notify');
}

errorHandler = function (error) {
    if (notify) {
        notify.onError({
            title: "Gulp error in " + error.plugin,
            message: error.toString()
        })(error);
    }
    this.emit('end');
};

gulp.task('less', function () {
    return gulp.src([
        'resources/assets/less/style.less',
        'resources/assets/less/print.less'
    ]).pipe(plumber({
        errorHandler: errorHandler
    })).pipe(sourcemaps.init()).pipe(less())/*.pipe(minify())*/.pipe(sourcemaps.write()).pipe(gulp.dest('public/assets/css/frontend/'));
});

gulp.task('publish-css-components', function () {
    return gulp.src([
        'node_modules/jquery-ui/themes/base/**/*'
    ]).pipe(gulp.dest('public/assets/css/frontend/components/'));
});

gulp.task('publish-css', function () {
    return gulp.src([
        'node_modules/jquery-bar-rating/dist/themes/fontawesome-stars.css',
        'resources/assets/js/frontend/Spectrum/spectrum.css',
        'resources/assets/css/frontend/jquery.fileupload.css',
        'resources/assets/css/frontend/flag-icon.css',
        'resources/assets/css/frontend/dropzone.css'
    ]).pipe(gulp.dest('public/assets/css/frontend/'));
});

gulp.task('js', function () {
    return gulp.src([
        'resources/assets/js/frontend/numeric.js',
        'resources/assets/js/frontend/cookie.js',
        'resources/assets/js/frontend/geocoder-util.js',
        'resources/assets/js/frontend/jquery.min.js',
        'node_modules/X-editable/src/inputs/combodate/lib/moment.min.js',
        'node_modules/waypoints/lib/jquery.waypoints.js',
        'resources/assets/js/pages/jquery-ui.min.js',
        'resources/assets/js/frontend/notification.js',
        'resources/assets/js/frontend/input.js',
        'resources/assets/js/frontend/search.js',
        'resources/assets/js/frontend/newsletter.js',
        'resources/assets/js/frontend/character_counter.js',
        'resources/assets/js/frontend/bootstrap4navbar-hover.js',
        'resources/assets/js/frontend/tree-view.js'
    ]).pipe(plumber({
        errorHandler: errorHandler
    })).pipe(concat('scripts.js')).pipe(uglify()).pipe(gulp.dest('public/assets/js/frontend/'));
});

gulp.task('publish-js-components', function () {
    return gulp.src([
        'node_modules/jquery-ui/ui/widgets/*.js',
    ]).pipe(gulp.dest('public/assets/js/frontend/components/'));
});

gulp.task('publish-js', function () {
    return gulp.src([
        //'resources/assets/js/frontend/popper-1.14.3.js',
        //'resources/assets/js/frontend/bootstrap-4.1.3.js',
        'resources/assets/js/frontend/index.js',
        'resources/assets/js/frontend/dashboard.js',
        'resources/assets/js/frontend/user-account.js',
        'resources/assets/js/frontend/flag-picker.js',
        'resources/assets/js/frontend/select2flags.js',
        'resources/assets/js/frontend/jobs.js',
        'resources/assets/js/frontend/events.js',
        'resources/assets/js/frontend/carousel.js',
        'resources/assets/js/frontend/ScrollMagic/**/*.*',
        'resources/assets/js/frontend/news-widget.js',
        'resources/assets/js/frontend/events-widget.js',
        'resources/assets/js/frontend/particlemap.min.js',
        'node_modules/jquery.counterup/jquery.counterup.js',
        'node_modules/X-editable/src/inputs/combodate/lib/combodate.js',
        'node_modules/bootbox/bootbox.min.js',
        'node_modules/jquery-bar-rating/dist/jquery.barrating.min.js',
        'resources/assets/js/frontend/search-by-location.js',
        'resources/assets/js/frontend/search-by-location-id.js',
        'resources/assets/js/frontend/search-by-business-categories.js',
        'resources/assets/js/frontend/member-selector.js',
        'resources/assets/js/frontend/Spectrum/spectrum.js',
        'resources/assets/js/frontend/jQueryFileUpload/jquery.iframe-transport.js',
        'resources/assets/js/frontend/jQueryFileUpload/jquery.fileupload-validate.js',
        'resources/assets/js/frontend/jQueryFileUpload/jquery.fileupload.js',
        'resources/assets/js/frontend/jQueryFileUpload/jquery.fileupload-process.js',
        'resources/assets/js/frontend/jQueryFileUpload/jquery.fileupload-video.js',
        'resources/assets/js/frontend/jQueryMask/dist/jquery.mask.js',
        'resources/assets/js/frontend/dropzone.js',
    ]).pipe(plumber({
        errorHandler: errorHandler
    })).pipe(uglify()).pipe(gulp.dest('public/assets/js/frontend/'));
});

gulp.task('publish-json', function () {
    return gulp.src([
        'resources/assets/js/frontend/countries.geo.json',
    ]).pipe(plumber({
        errorHandler: errorHandler
    })).pipe(gulp.dest('public/assets/js/frontend/'));
});

gulp.task('publish-fonts', function () {
    gulp.src('resources/assets/fonts/**/*.*')
        .pipe(gulp.dest('public/assets/fonts/'));
});

gulp.task('publish-images', function () {
    gulp.src('resources/assets/img/frontend/**/*.+(png|gif|jpeg|jpg|svg)')
        .pipe(imagemin({
            verbose: true
        })).pipe(gulp.dest('public/assets/img/frontend/'));
});

gulp.task('publish-favicon-images', function () {
    gulp.src('resources/assets/img/favicon/**/*')
        .pipe(gulp.dest('public/assets/img/favicon/'));
});

gulp.task('publish-flags-images', function () {
    gulp.src('resources/assets/img/frontend/flags/**/*.*')
        .pipe(gulp.dest('public/assets/img/frontend/flags'));
});

gulp.task('publish-videos', function () {
    gulp.src('resources/assets/img/frontend/**/*.+(mp4|ogv|ogm|ogg|webm)')
        .pipe(gulp.dest('public/assets/img/frontend/'));
});

gulp.task('publish-lightbox2', function () {
    return gulp.src([
        'node_modules/lightbox2/src/**/*.*',
    ]).pipe(gulp.dest('public/assets/vendors/lightbox2/'));
});

gulp.task('publish-printjs', function () {
    gulp.src('node_modules/print-js/dist/**/*.*')
        .pipe(gulp.dest('public/assets/vendors/print-js/'));
});

gulp.task('default', ['less', 'publish-css-components', 'publish-css', 'js', 'publish-js-components', 'publish-js', 'publish-favicon-images', 'publish-flags-images', 'publish-images', 'publish-videos', 'publish-fonts', 'publish-json', 'publish-lightbox2', 'publish-printjs']);
gulp.task('upgrade', ['less', 'publish-css-components', 'publish-css', 'js', 'publish-js-components', 'publish-js', 'publish-images', 'publish-videos', 'publish-fonts', 'publish-json', 'publish-lightbox2', 'publish-printjs']);

gulp.task('watch', function () {
    gulp.watch('resources/assets/less/**/*.less', ['less']);
    gulp.watch('resources/assets/js/frontend/**/*.js', ['js', 'publish-js']);
    gulp.watch('resources/assets/img/frontend/**/*.+(png|gif|jpeg|jpg|svg)', ['publish-images']);
    gulp.watch('resources/assets/img/frontend/**/*.+(mp4|ogv|ogm|ogg|webm)', ['publish-videos']);
    gulp.watch('resources/assets/fonts/**/*.*', ['publish-fonts']);
});
