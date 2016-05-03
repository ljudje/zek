overwrite_images_with_local_paths = ->
	$('.imgix-fluid').each (i, el) ->
		path = $(el).data('src')
		path = path.replace('http://zek.imgix.net/', '/assets/projects/')
		$(el).attr('data-src', path)
		bim = "url(#{path})"
		if el.tagName == 'IMG'
			el.src = path
		else
			$(el).css(backgroundImage: "#{bim}", backgroundSize: "cover")

style_content = ->
	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')

	$('.contents .youtube iframe, .contents .vimeo iframe').each (i, v) ->
		width = $(v).width()
		height = width / 1.6
		$(v).height(height)	

if document.location.href.indexOf('localhost') > -1
	overwrite_images_with_local_paths()

$(document).ready ->
	$(document).foundation()

	style_content()

	if document.location.href.indexOf('localhost') == -1
		imgix.onready () ->
			imgix.fluid
				updateOnResizeDown: true,
				pixelStep: 5,
				autoInsertCSSBestPractices: true,
				lazyLoad: true,
				lazyLoadOffsetVertical: 1000,
				lazyLoadColor: false

	$(document).on 'keydown', (e) ->
		# left
		if e.keyCode == 37
			$el = $('body.project a.prev-arrow')
			if  $el.length > 0
				document.location.href = $el.attr('href')
		# right
		if e.keyCode == 39
			$el = $('body.project a.next-arrow')
			if  $el.length > 0
				document.location.href = $el.attr('href')

	if $('body').hasClass('home')
		$('.logo a').click (e) ->
			e.preventDefault()
			$('html, body').animate(scrollTop: 0)

