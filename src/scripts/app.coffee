$(document).ready ->
	$(document).foundation()

	$('nav.sticky').on 'sticky.zf.stuckto:top', (e) ->	
		$('nav.sticky .logo').animate(paddingTop: '0px')

	$('nav.sticky').on 'sticky.zf.unstuckfrom:top', (e) ->	
		$('nav.sticky .logo').animate(paddingTop: '44px')

	$('nav.sticky .logo').css(paddingTop: '44px')

	$('.contents p').each (i, p) ->
		if $(p).find('img').length == 0 and $(p).hasClass('imgix-fluid') == false
			$(p).addClass('no-img')

	$('.imgix-fluid').each (i, el) ->
		# ix = new imgix.URL('//zek.imgix.com/' + $(el).data('local'))
		ix = new imgix.URL($(el).data('src'))
		ix.getColors (colors) ->
			$(el).css(backgroundColor: colors[0])