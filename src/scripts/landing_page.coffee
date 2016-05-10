# # # # # # # # # # # # # # #
# # # LANDING PAGE  # # # # #
# # # # # # # # # # # # # # #

add_scroll_behaviour = ->
	$('.logo a').click (e) ->
		e.preventDefault()
		$('html, body').animate(scrollTop: 0)
		
add_winking_behaviour = ->
	$wel = $('.welcome-message p')

	first_interval = 1000

	wink = ->
		$wel.text('We’re the ZEK Crew ;)')
		setTimeout(un_wink, 180 + Math.random() * 40)

	un_wink = ->
		$wel.text('We’re the ZEK Crew :)')
		if first_interval
			setTimeout(wink, 1500)
			first_interval = false
		else
			setTimeout(wink, 2000 + Math.random() * 3000)

	wink()

add_ios_hovering_behaviour = ->
	$('.project').on 'touchstart', (e) ->
		$(e.targetElement).find('a').addClass('hover')
	$('.project').on 'touchend', (e) ->
		$('.project a').removeClass('hover')

module.exports = ->
	if $('body').hasClass('home')
		add_scroll_behaviour()
		add_winking_behaviour()
		add_ios_hovering_behaviour()