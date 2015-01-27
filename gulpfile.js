var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var notify = require('gulp-notify');

gulp.task('phpspec', function() {
    gulp.src('./src/**/*.php')
        .pipe(phpspec('', {notify: true}))
        .on('error', notify.onError(testNotification('fail', 'phpspec')))
        .pipe(notify(testNotification('pass', 'phpspec')));
});

function testNotification(status, pluginName) {
    var options = {
        title:   ( status == 'pass' ) ? 'Tests Passed' : 'Tests Failed',
        message: ( status == 'pass' ) ? '\n\nAll tests have passed!\n\n' : '\n\nOne or more tests failed...\n\n',
        icon:    __dirname + '/node_modules/gulp-' + pluginName +'/assets/test-' + status + '.png'
    };

    return options;
}

gulp.task('tdd', function() {
    gulp.watch(['./spec/**/*.php', './src/**/*.php'], ['phpspec']);
});

gulp.task('default', ['tdd']);
