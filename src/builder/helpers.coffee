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
		if file.paths.href == "/projects/#{path}"
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
		# If we didn't specify a scope
		if options == undefined
			options = name
			name = from
			from = undefined
			path = options.data.root.paths
		else
			path = from.paths


		dir = encodeURIComponent(path.dir)
		parent = encodeURIComponent(path.name)
		name = encodeURIComponent(name)
		collection_path = "#{parent}/#{name}"
		full_path = "#{dir}/#{parent}/#{name}"
		img = _find_image_asset(@image_assets, collection_path)

		data =
			src: collection_path
			width: img.geom_width
			height: img.geom_height
			y_ratio: img.geom_y_ratio
			x_ratio: 100 / img.geom_y_ratio
			name: name

		return options.fn(data)

	simple_image_hb: (image) ->
		"![](#{full_path})"

	spacer: ->
		new Handlebars.SafeString("<div class='spacer'></div>")
		
	padded_content: () ->
		params = Array.prototype.slice.call(arguments)
		options = params[params.length - 1]
		return "<div class='padded'>" + options.fn(this) + "</div>"

	markdown: () ->
		params = Array.prototype.slice.call(arguments)
		mdo = marked(params[0])
		return new Handlebars.SafeString(mdo)

	cta: () ->
		params = Array.prototype.slice.call(arguments)
		options = params[params.length - 1]
		return "<p class='cta'>" + options.fn(this) + "</p>"
