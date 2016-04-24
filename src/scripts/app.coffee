if document.location.href.indexOf('localhost') > -1
	$('.imgix-fluid').each (i, el) ->
		path = $(el).data('src')
		path = path.replace('http://zek.imgix.net/', '/assets/projects/')
		$(el).attr('data-src', path)
		bim = "url(#{path})"
		if el.tagName == 'IMG'
			el.src = path
		else
			$(el).css(backgroundImage: "#{bim}", backgroundSize: "cover")
		console.log('ok', bim, $(el).attr('style'))

$(document).ready ->
	$(document).foundation()

	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')
			

	if document.location.href.indexOf('localhost') == -1
		imgix.onready () ->
			imgix.fluid
				updateOnResizeDown: true,
				pixelStep: 5,
				autoInsertCSSBestPractices: true,
				lazyLoad: true,
				lazyLoadOffsetVertical: -200,
				lazyLoadColor: false