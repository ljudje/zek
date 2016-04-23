image_size = require('image-size')
logging = false

_LOG_ = (a, b, c, d) ->
	if logging == true
		console.log a, b or '', c or '', d or ''

assign_layouts_by_pattern = (config, files) ->
  _LOG_ 'file name pattern matcher'
  pattern = new RegExp(config.pattern)
  for file of files
    if pattern.test(file)
      _LOG_ '# Found matching layout', files[file].paths.name, config.pattern
      _f = files[file]
      if !_f.layout
        _f.layout = config.layout

assign_layouts_by_collection = (config, files) ->
  _LOG_ 'collection matcher'
  for file of files
    if files[file].collection.indexOf(config.collection) > -1
      _LOG_ '# Found matching layout', files[file].paths.name, config.collection
      _f = files[file]
      if !_f.layout
        _f.layout = config.layout

module.exports =

	remove_drafts: (config) ->
		(files, metalsmith, done) ->
			for file of files
				if files[file].draft == true
					_LOG_ '# Deleting image', files[file].name
					delete files[file]
			done()

	remove_images_from_scope: (config) ->
		(files, metalsmith, done) ->
			image_extensions = ['.jpg',	'.jpeg', '.png',	'.gif',	'.webp', '.svg']
			for file of files
				current_extension = files[file].paths.ext.toLowerCase()
				if image_extensions.indexOf(current_extension) > -1
					delete files[file]
			done()

	remove_ignored_files: (config) ->
		(files, metalsmith, done) ->
			ignored_files = [ '.DS_Store' ]
			for file of files
				current_file = files[file].paths.base
				if ignored_files.indexOf(current_file) > -1
					delete files[file]
			done()

	assign_layouts: (options) ->
		_LOG_ options
		(files, metalsmith, done) ->
			for i of options
				config = options[i]
				_LOG_ 'looking at', config
				if config.collection != undefined
					assign_layouts_by_collection config, files
				if config.pattern != undefined
					assign_layouts_by_pattern config, files
			done()

	image_dimensions: ->
		(files, metalsmith, done) ->
			metalsmith._metadata.image_assets ||= []
			image_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg']
			for i of files
				f = files[i]
				current_extension = f.paths.ext.toLowerCase()
				# This is an image
				if image_extensions.indexOf(current_extension) > -1
					# It's in the 'projects/' folder
					if f.paths.dir.indexOf('projects/') == 0
						# Read dimensions
						dimensions = image_size(f.contents)
						# Cache dimensions
						f.geom_width = dimensions.width
						f.geom_height = dimensions.height
						f.geom_y_ratio = dimensions.width / dimensions.height
						# Cache src path (remove projects/ prefix)
						href = f.paths.href
						f.paths.src = href.substr(9, href.length)
						# Push to image assets
						metalsmith._metadata.image_assets.push(f)

			# console.log('image assets', metalsmith._metadata.image_assets)
			done()

	debug_metadata: ->
		(files, metalsmith, done) ->
			mdata = metalsmith.metadata()
			_LOG_ '# Site metadata:'
			_LOG_ mdata

			done()

