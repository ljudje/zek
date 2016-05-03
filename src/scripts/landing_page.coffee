# # # # # # # # # # # # # # #
# # # LANDING PAGE  # # # # #
# # # # # # # # # # # # # # #

add_scroll_behaviour = ->
	$('.logo a').click (e) ->
		e.preventDefault()
		$('html, body').animate(scrollTop: 0)
		
module.exports = ->
	if $('body').hasClass('home')
		add_scroll_behaviour()
