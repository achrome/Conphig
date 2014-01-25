module.exports = function(grunt) {

  grunt.initConfig({
    phpcs: {
      application: {
        dir: 'src'
      },
      options: {
        standard: 'PSR1',
        report: 'summary'
      }
    },
    phpunit: {
      classes: {
        dir: 'tests'
      }
    }
  });

  grunt.loadNpmTasks('grunt-phpcs');
  grunt.loadNpmTasks('grunt-phpunit');
  
  grunt.registerTask('default', ['phpcs']);
  grunt.registerTask('test', ['phpunit']);
}