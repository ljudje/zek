# # # # # # # # # # # # # # #
# # # PROJECTS PAGE # # # # #
# # # # # # # # # # # # # # #

resize_videos = (e) ->
	$('.contents .youtube iframe, .contents .vimeo iframe').each (i, video) ->
		width = $(video).width()
		height = width / 1.6
		$(video).height(height)	
		$(video).parent().height(height + 1)

resize_embeds = (e) ->
	$('.contents .embed iframe').each (i, embed) ->
		height = $(embed).height()
		$(embed).parent().height(height)

annotate_paragraphs = ->
	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')

style_content = ->
	annotate_paragraphs()
	resize_videos()
	resize_embeds()
	$(window).resize ->
		resize_videos()
		resize_embeds()

module.exports = ->
	if $('body').hasClass('project')
		style_content()