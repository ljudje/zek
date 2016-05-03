# # # # # # # # # # # # # # #
# # # EXTERNAL LINKS  # # # #
# # # # # # # # # # # # # # #

get_position = (str, m, i) ->
	str.split(m, i).join(m).length

path = document.location.href
third_slash_index = get_position(path, '/', 3)
path = path.substr(0, third_slash_index)

ensure_blank_target = (index, link) ->
	# If <a> href attribute begins with http://{{ our_hostname }}/
	if link.href.indexOf(path) != 0
		# Set it's target to _blank
		$(link).attr('target': '_blank')

module.exports = ->
	$('a').each(ensure_blank_target)