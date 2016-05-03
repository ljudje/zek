# # # # # # # # # # # # # # #
# # # PROJECTS PAGE # # # # #
# # # # # # # # # # # # # # #

resize_videos = (e) ->
	console.log('lol', document)
	$('.contents .youtube iframe, .contents .vimeo iframe').each (i, v) ->
		width = $(v).width()
		height = width / 1.6
		$(v).height(height)	
		$(v).parent().height(height + 1)

annotate_paragraphs = ->
	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')

style_content = ->
	annotate_paragraphs()
	resize_videos()
	$(window).resize(resize_videos)

module.exports = ->
	if $('body').hasClass('project')
		style_content()