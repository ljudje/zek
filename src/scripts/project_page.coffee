# # # # # # # # # # # # # # #
# # # PROJECTS PAGE # # # # #
# # # # # # # # # # # # # # #

resize_videos = (selector) ->
	selector ?= '.contents .youtube iframe, .contents .vimeo iframe'
	$(selector).each (i, video) ->
		width = $(video).width()
		height = width / 1.6
		$(video).height(height)	
		$(video).parent().height(height + 1)

resize_embeds = (selector) ->
	selector ?= '.contents .embed iframe, .contents .embed video'
	$(selector).each (i, embed) ->
		spec_width = $(embed).attr('width')
		spec_height = $(embed).attr('height')

		# If the element scales according to a ratio
		if spec_width
			# If the element has a pixel ratio specified in terms of width and height
			if spec_width != '100%'
				# Read spec ratio
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
		# else, the element scales automatically
		else 
				# We might have a height by this time
				height = $(embed).height()

		# If we succeeded in obtaining a height
		if height != NaN
			# Ensure the parent doesn't leave a bottom gap
			$(embed).parent().height(height)

		# # If we're dealing with a video and we're iterating over a larger collection of items
		# if embed.tagName == 'VIDEO' and !selector.tagName
		# 	# Listen for metadata
		# 	$(embed).on 'loadedmetadata', ->
		# 		console.log('loaded metadata')
		# 		# Resize the embed
		# 		resize_embeds(embed)
		# 		# and stop listening for changes
		# 		$(embed).off 'loadedmetadata'
		
resize_project_nav_images = ->
	window_height = $(window).height()
	obscured = $('.logo').outerHeight() + $('.prev-thumb a .title, .next-thumb a .title').outerHeight() + $('footer').outerHeight()
	remain = window_height - obscured
	$('.prev-thumb .imgix-fluid, .next-thumb .imgix-fluid').height(remain)
	$('.prev-thumb .imgix-fluid, .next-thumb .imgix-fluid').width('100%')

annotate_paragraphs = ->
	$('.contents p').each (i, p) ->
		if $(p).find('img, embed, .responsive-image, video').length == 0
			$(p).addClass('no-img')
			
style_content = ->
	annotate_paragraphs()

	resize_layout = ->
		resize_videos()
		resize_embeds()
		resize_project_nav_images()

	resize_layout()

	$(window).on('resize', resize_layout)
	$(window).on('orientationchange', resize_layout)

add_arrow_hover_behaviour = ->
	$('.next-thumb a').mouseenter (e) ->
		$('.next-arrow').addClass('invert') unless $('.next-arrow').hasClass('invert')
	$('.next-thumb a').mouseleave (e) ->
		$('.next-arrow').removeClass('invert')
	
	$('.prev-thumb a').mouseenter (e) ->
		$('.prev-arrow').addClass('invert') unless $('.prev-arrow').hasClass('invert')
	$('.prev-thumb a').mouseleave (e) ->
		$('.prev-arrow').removeClass('invert')

add_zoom_detection_behaviour = ->
	handle_scale_change = (event) ->
       scale = event.originalEvent.scale
       if scale > 1
       		$('header').hide()
       	else
       		$('header').show()
		
	$('body').bind "gesturestart", handle_scale_change
	$('body').bind "gesturechange", ->
		setTimeout(handle_scale_change, 100)
	$('body').bind "gestureend", ->
		setTimeout(handle_scale_change, 200)

autoplayable_videos = []

autoplay_videos_in_viewport = ->
	scroll_top = $(window).scrollTop()
	scroll_bottom = scroll_top + $(window).height()

	for video in autoplayable_videos
		video_top = $(video).offset().top
		video_bottom = video_top + $(video).height()
		
		# If the video is within viewport
		if scroll_top < video_bottom and scroll_bottom > video_top
			$(video).get(0).play()
			# $(video).removeClass('paused').addClass('playing')
		else # the video is not within the viewport
			$(video).get(0).pause()
			# $(video).removeClass('playing').addClass('paused')

loop_videos_only_in_viewport = ->
	$('video').each (i, video) ->
		if $(video).attr('autoplay') == 'autoplay'
			$(video).attr('autoplay', false)
			$(video).get(0).pause()
			autoplayable_videos.push(video)

	unless Foundation.MediaQuery.current == 'small'
		$(window).on 'scroll', Foundation.util.throttle(autoplay_videos_in_viewport, 30)
		autoplay_videos_in_viewport()

module.exports = ->
	if $('body').hasClass('project')
		style_content()
		add_arrow_hover_behaviour()
		add_zoom_detection_behaviour()
		loop_videos_only_in_viewport()
