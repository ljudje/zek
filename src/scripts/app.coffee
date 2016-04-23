$(document).ready ->
	$(document).foundation()

	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')
			
	imgix.onready () ->
		imgix.fluid
			updateOnResizeDown: true,
			pixelStep: 5,
			autoInsertCSSBestPractices: true,
			lazyLoad: true,
			lazyLoadOffsetVertical: -200,
			lazyLoadColor: false