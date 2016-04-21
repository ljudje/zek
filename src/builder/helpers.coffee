marked = require('marked')
Handlebars = require('handlebars')

_project_image_hb = (project, image) ->
	'![](' + _project_image_url(project, image) + ')'

_project_image_url = (project, image) ->
	'/assets/projects/' + project + '/' + image

_image_hb = (paths, image) ->
	"![](/assets/#{paths.dir}/#{paths.name}/#{image})"

_find_image_asset = (image_assets, path) ->
	found = false
	for ile in image_assets
		if ile.paths.src == path
			found = ile

	found

module.exports =
	debug_handlebars: (optional) ->
		if optional != undefined
			console.log "#{optional}:", @[optional]
		else
			console.log 'Debug:', @

	project_image_url: (image) ->
		_project_image_url(@paths.name, image)

	project_image_hb: (image) ->
		_project_image_hb(@paths.name, image)

	image_hb: (name, options) ->
		paths = options.data.root.paths

		full_path = "/assets/#{paths.dir}/#{paths.name}/#{name}"
		img = _find_image_asset(@image_assets, full_path)
		
		data =
			src: full_path
			width: img.geom_width
			height: img.geom_height
			y_ratio: img.geom_y_ratio
			x_ratio: 100 / img.geom_y_ratio
			name: name

		return options.fn(data)

	simple_image_hb: (image) ->
		"![](#{full_path})"
		
	padded_content: () ->
		params = Array.prototype.slice.call(arguments)
		options = params[params.length - 1]
		return "<div class='padded'>" + options.fn(this) + "</div>"

	markdown: () ->
		params = Array.prototype.slice.call(arguments)
		return marked(params[0])

	cta: () ->
		params = Array.prototype.slice.call(arguments)
		options = params[params.length - 1]
		return "<p class='cta'>" + options.fn(this) + "</p>"
