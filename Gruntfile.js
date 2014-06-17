/**
 * 
 * @author    Ashwin Mukhija
 * @package   Conphig
 */

'use strict';

module.exports = function(grunt) {
  grunt.initConfig({
    watch: {
      files: ['src/**/*', 'tests/**/*', 'autoload.php', 'phpunit.xml', 'Gruntfile.js'],
      tasks: ['check']
    },
    phpunit: {
      options: {
        bin: 'vendor/bin/phpunit --coverage-html coverage',
        colors: true
      },
      classes: {
        dir: 'tests'
      }
    },
    phpcs: {
      application: {
        dir: ['src/**/*.php']
      },
      options: {
        bin: 'vendor/bin/phpcs',
        verbose: true,
        standard: 'PSR2'
      }
    }
  });
  
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-phpcs');
  
  grunt.registerTask('check', ['phpunit', 'phpcs']);
  grunt.registerTask('default', ['watch']);
};
