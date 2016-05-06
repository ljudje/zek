imagesLoaded = require('imagesloaded')

# # # # # # # # # # # # # # #
# # # HEIGHT EQUALIZER  # # #
# # # # # # # # # # # # # # #

# Which elements to equalize by default
eqalize_selector = '.equalize'

equalize_elements = (selector) ->
	selector ?= eqalize_selector
	# Initial values
	current_row = -1
	# Elements in the current row
	el_row = []
	# Max height
	max_height = 0

	# Iterate through all the target elements
	$(selector).each (i, el) ->
		# Memorize the current element top offset as y, outerHeight as el_height
		y = Math.round($(el).offset().top)
		el_height = 0
		# If there are child nodes in the element
		if $(el).children().length > 0
			# Measure their cumulative height
			for child in $(el).children()
				el_height += $(child).outerHeight()
		# else, there is text inside
		else
			# If height has already been set
			if $(el).attr('style')
				# unset it
				$(el).css(height: 'auto')
			# Measure intrinsic height
			el_height = $(el).outerHeight()

		# If we're beginning a new row
		if y > current_row
			# While there are elements in the prevous el_row stack
			while el_row.length > 0
				# Pop them from the stack
				popped = el_row.pop()
				# and equalize their height
				$(popped).height(max_height)

			# Reset max_height
			max_height = 0
			y = Math.round($(el).offset().top)
			current_row = y

		# If the current element height is bigger than the max_height
		if el_height > max_height
			# memorize the max height
			max_height = el_height

		# Remember that this el is part of the current row
		el_row.push(el)

	# Equalize the last row
	while el_row.length > 0
		popped = el_row.pop()
		$(popped).height(max_height)

module.exports = (selector) ->
	handle_changes = ->
		equalize_elements(selector)

	handle_changes()

	$(document).imagesLoaded(handle_changes)
	$(window).on('resize', handle_changes)
	$(window).on('orientationchange', handle_changes)
