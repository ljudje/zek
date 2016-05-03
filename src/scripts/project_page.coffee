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
		# If the element scales according to a ratio
		if spec_width != '100%'
			# Read spec ratio
			spec_width = $(embed).attr('width')
			spec_height = $(embed).attr('height')
			ratio = spec_width / spec_height
			# Calculate new height from ratio
			width = $(embed).width()
			height = width * ratio
			# Resize embed
			$(embed).height(height)
		# else, the element stretches to 100% width
		else
			# Just ensure the desired height is remembered
			height = $(embed).height()

		# Ensure the parent doesn't leave a bottom gap
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