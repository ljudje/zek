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
	for file in image_assets
		if file.paths.src == path
			found = file

	found

_find_image_asset_2 = (image_assets, path) ->
	found = false
	once = false
	for file in image_assets
		unless once
			once = true

		if file.paths.src == path
			found = file

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

	image_hb: (from, name, options) ->
		if options == undefined
			options = name
			name = from
			from = undefined

		paths = options.data.root.paths

		if from != undefined
			full_path = "/assets/#{from.paths.dir}/#{from.paths.name}/#{name}"
			img = _find_image_asset_2(@image_assets, full_path)
		else
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
