
_project_image_hb = (project, image) ->
	'![](' + _project_image_url(project, image) + ')'

_project_image_url = (project, image) ->
	'/assets/projects/' + project + '/' + image

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