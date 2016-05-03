# # # # # # # # # # # # # # #
# # # PROJECTS PAGE # # # # #
# # # # # # # # # # # # # # #

style_content = ->
	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')

	$('.contents .youtube iframe, .contents .vimeo iframe').each (i, v) ->
		width = $(v).width()
		height = width / 1.6
		$(v).height(height)	

module.exports = ->
	if $('body').hasClass('project')
		style_content()