/**
 * A Gruntfile to ensure QA tasks are run in succession.
 * 
 * @author    Ashwin Mukhija
 * @package   Conphig
 */

'use strict';

module.exports = function(grunt) {
  grunt.initConfig({
    phpunit: {
      classes: {
        dir: 'tests'
      }
    }
  });
  
  grunt.loadNpmTasks('grunt-phpunit');
  
  grunt.registerTask('default', function() {
    grunt.task.run('phpunit');
  });
}