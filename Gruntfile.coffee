module.exports = (grunt) ->
	# Required tasks
	grunt.loadNpmTasks('grunt-exec')
	grunt.loadNpmTasks('grunt-haml')
	grunt.loadNpmTasks('grunt-contrib-sass')
	grunt.loadNpmTasks('grunt-contrib-coffee')
	grunt.loadNpmTasks('grunt-contrib-copy')
	grunt.loadNpmTasks('grunt-contrib-clean')
	grunt.loadNpmTasks('grunt-contrib-watch')

	# Configuration
	grunt.initConfig
		pkg: grunt.file.readJSON('package.json')

		clean: [ "build" ]

		watch:
			options:
				nospawn: true
				livereload: true

			coffee:
				files: ['src/scripts/{,*/}*.coffee']
				tasks: ['coffee']

			sass:
				files: ['src/stylesheets/{,*/}*.{scss,sass}'],
				tasks: ['sass']

			haml:
			  files: ['src/templates/{,*/}*.haml'],
			  tasks: ['haml', 'exec']

			handlebars:
				files: [
					'src/scripts/templates/compiled/{,*/}*.hbt'
				]
				tasks: ['exec']

			rebuild:
				files: ['content/{,*/}*.md', 'build.coffee']
				tasks: ['exec']

			assets:
				files: ['src/assets/{,*/}*.*']
				tasks: ['copy:assets']

		sass:
			options:
				loadPath: [
					'node_modules/foundation-sites/scss'
					'node_modules/motion-ui/src'
				]
			dist:
				options:
					sourcemap: 'none'
					style: 'nested'
				files: [{
					expand: true
					cwd: 'src/stylesheets'
					src: ['*.scss', '*.sass']
					dest: 'build/assets/css'
					ext: '.css'
				}]

		coffee:
			dist:
				files: [{
					expand: true
					cwd: 'src/scripts'
					src: '{,*/}*.coffee'
					dest: 'build/assets/js'
					ext: '.js'	
				}]

		copy:
			depjs:
				files:
					'build/assets/js/foundation.min.js': 'node_modules/foundation-sites/dist/foundation.min.js'
					'build/assets/js/jquery.min.js': 'node_modules/jquery/dist/jquery.min.js'

			assets:
				files: [{
					expand: true
					cwd: 'src/assets'
					src: '{,*/}*.*'
					dest: 'build/assets'
				}]

			project_images:
				files: [{
					expand: true
					cwd: 'content/projects'
					src: '{,*/}*.{jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF}'
					dest: 'build/assets/projects'
				}]		

		haml:
			compile:
				files: [{
					expand: true
					cwd: 'src/templates'
					src: '{,*/}*.haml'
					dest: 'src/templates/compiled'
					ext: '.hbt'	
				}]
				options:
					language: 'ruby'
					target: 'html'
					rubyHamlCommand: 'haml -t indented'

		exec:
			metalsmith: './node_modules/coffee-script/bin/coffee build.coffee'

		# useminPrepare:
		# 	html: [
		# 		'build/people/*/index.html'
		# 		'build/projects/*/index.html'
		# 		'build/index.html'
		# 	]
		# 	options:
		# 		dest: 'build'

	# Subtasks
	grunt.registerTask('wipe', ['clean'])
	grunt.registerTask('styles', ['sass'])
	grunt.registerTask('scripts', ['coffee'])
	grunt.registerTask('templates', ['haml'])
	grunt.registerTask('content', ['exec', 'copy'])
	# grunt.registerTask('optimization', [
	# 	'useminPrepare'
	# 	'concat'
	# 	'cssmin'
	# 	'uglify'
	# 	'filerev'
	# 	'usemin'
	# ])

	# Main build task
	grunt.registerTask('build',  [
		'wipe'
		'scripts'
		'styles'
		'templates'
		'content'
		# 'optimization'
	])
	grunt.registerTask('dev', ['build', 'watch'])