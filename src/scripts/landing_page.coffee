# # # # # # # # # # # # # # #
# # # LANDING PAGE  # # # # #
# # # # # # # # # # # # # # #

add_scroll_behaviour = ->
	$('.logo a').click (e) ->
		e.preventDefault()
		$('html, body').animate(scrollTop: 0)
		
add_winking_behaviour = ->
	$wel = $('.welcome-message p')
	console.log('w1', $wel.length)

	wink = ->
		$wel.text('We’re the ZEK Crew ;)')
		setTimeout(un_wink, 180)

	un_wink = ->
		$wel.text('We’re the ZEK Crew :)')
		setTimeout(wink, 2000 + Math.random() * 3000)

	wink()

	console.log 'added winking behavour'

module.exports = ->
	if $('body').hasClass('home')
		add_scroll_behaviour()
		add_winking_behaviour()