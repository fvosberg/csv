var gulp = require('gulp');
var exec = require('child_process').exec;
var path = require('path');
var fs = require('fs');

gulp.task('test', function() {
	var class_file_to_test_file = function(class_path) {
		var test_file = class_path.replace(/\/Classes\//g, '/Tests/Unit/');
		return test_file.slice(0, -4) + 'Test.php';
	};

	gulp.watch('Classes/**/*.php', function(event) {
		var test_file = class_file_to_test_file(event.path);
		var test_command = 'vendor/bin/phpunit ' + test_file;
		console.log(test_command);
		exec(test_command, function(error, stdout) {
			console.log(stdout);
		});
	});
});