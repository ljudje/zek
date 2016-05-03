# # # # # # # # # # # # # # #
# # # KEYBOARD NAV  # # # # #
# # # # # # # # # # # # # # #

handle_left_key = (e) ->
	$el = $('body.project a.prev-arrow')
	if  $el.length > 0
		document.location.href = $el.attr('href')

handle_right_key = (e) ->
	$el = $('body.project a.next-arrow')
	if  $el.length > 0
		document.location.href = $el.attr('href')

module.exports = ->
	if $('body').hasClass('project')
		$(document).on 'keydown', (e) ->
			# left
			if e.keyCode == 37
				handle_left_key()
			# right
			if e.keyCode == 39
				handle_right_key()