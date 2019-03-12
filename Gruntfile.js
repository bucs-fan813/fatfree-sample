module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		copy : {
			js : {
				files : [ {
					expand : true,
					cwd : 'node_modules/jquery/dist',
					src : 'jquery.js',
					dest : 'static/tmp/js'
				}, {
					expand : true,
					cwd : 'node_modules/bootstrap/dist/js',
					src : 'bootstrap.bundle.js',
					dest : 'static/tmp/js'
				}, {
					expand : true,
					cwd : 'node_modules/scrollreveal/dist',
					src : 'scrollreveal.js',
					dest : 'static/tmp/js'
				}, {
					expand : true,
					cwd : 'node_modules/magnific-popup/dist',
					src : 'jquery.magnific-popup.js',
					dest : 'static/tmp/js'
				}, {
					expand : true,
					cwd : 'node_modules/jquery.easing',
					src : 'jquery.easing.js',
					dest : 'static/tmp/js'
				}, 
//				{
//					expand : true,
//					cwd : 'node_modules/startbootstrap-creative/js',
//					src : 'creative.js',
//					dest : 'static/tmp/js'
//				}
				]
			},
			css : {
				files : [ {
					expand : true,
					cwd : 'node_modules/bootstrap/dist/css',
					src : 'bootstrap.css',
					dest : 'static/tmp/css'
				}, {
					expand : true,
					cwd : 'node_modules/bootstrap/dist/css',
					src : 'boostrap-theme.css',
					dest : 'static/tmp/css'
				}, {
					expand : true,
					cwd : 'node_modules/bootstrap/dist/css',
					src : 'boostrap.css.map',
					dest : 'static/tmp/css'
				}, {
					expand : true,
					cwd : 'node_modules/magnific-popup/dist',
					src : '*.css',
					dest : 'static/tmp/css'
				}, {
					expand : true,
					cwd : 'node_modules/font-awesome/css',
					src : 'font-awesome.css',
					dest : 'static/tmp/css'
				}, {
					expand : true,
					cwd : 'node_modules/startbootstrap-creative/css',
					src : 'creative.css',
					dest : 'static/tmp/css'
				} ]
			},
			fonts : {
				files : [ {
					expand : true,
					cwd : 'node_modules/font-awesome/fonts',
					src : '*.*',
					dest : 'static/fonts'
				} ]
			}

		// end sb_creative
		}, // end copy
		concat : {
			js : {
				src : [ 'static/tmp/js/jquery.js',
						'static/tmp/js/boostrap.bundle.js',
						'static/tmp/js/*.js', 'template/sb-creative/js/*.js' ],
				dest : 'static/dist/script.js',
				options : {
					separator : ';\n'
				}
			},
			css : {
				src : [ 'static/tmp/css/*.css', 'template/sb-creative/css/*.css' ],
				dest : 'static/dist/style.css',
				options : {
				// separator: ';\n'
				}

			}
		}
	});

	// // Deletes all intermediate files created for the build, but skips
	// // our custom files
	// clean: ['static/tmp']
	// Load required modules
	grunt.loadNpmTasks('grunt-contrib-copy');
	// grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-concat');
	// grunt.loadNpmTasks('grunt-contrib-uglify');
	// grunt.loadNpmTasks('grunt-contrib-cssmin');
	// grunt.loadNpmTasks('grunt-contrib-compress');
	// grunt.loadNpmTasks('grunt-contrib-clean');

	// Task definitions
	grunt.registerTask('default', [ 'copy', 'concat' ]);
	// grunt.registerTask('sb_creative', [ 'sb_creative:copy',
	// 'sb_creative:concat' ]);
	// grunt.registerTask('default', ['copy', 'less', 'concat',
	// 'cssmin','uglify',
	// 'compress','clean']);

};