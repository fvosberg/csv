var gulp = require('gulp');
var exec = require('child_process').exec;
var path = require('path');
var fs = require('fs');

gulp.task('phpunit', function() {
	var class_file_to_test_file = function(class_path) {
		var test_file = class_path.replace(/\/Classes\//g, '/Tests/Unit/');
		return test_file.slice(0, -4) + 'Test.php';
	};

	var exec_phpunit_for_test_file = function(path) {
		var test_command = 'vendor/bin/phpunit ' + path;
		console.log(test_command);
		exec(test_command, function(error, stdout) {
			console.log(stdout);
		});
	};

	gulp.watch('Classes/**/*.php', function(event) {
		var test_file = class_file_to_test_file(event.path);
		exec_phpunit_for_test_file(test_file);
	});

	gulp.watch('Tests/Unit/**/*Test.php', function(event) {
		var test_file = event.path;
		exec_phpunit_for_test_file(test_file);
	});
	exec_phpunit_for_test_file('Tests');
});
