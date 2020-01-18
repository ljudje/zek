# # # # # # # # # # # # # # #
# # # RESPONSIVE IMAGES # # #
# # # # # # # # # # # # # # #

# imgix = require('imgix')
# conf = require('./config')

# https://res.cloudinary.com/zek/image/upload/netlify/sample.jpg → https://zekcrew.com/sample.jpg

CLOUDINARY_PATH = "https://res.cloudinary.com/zek/image/upload/netlify"

apply_img_src = (el, src) ->
	if el.tagName == 'IMG'
		el.src = "#{src}"
	else
		bkg_img = "url(#{src})"
		$(el).css(backgroundImage: "#{bkg_img}", backgroundSize: "cover", backgroundPosition: "center center")

# Replace responsive images with local paths (development mode helper)
overwrite_images_with_local_paths = ->
	$('.imgix-fluid').each (i, el) ->
		path = $(el).data('src')
		apply_img_src(el, path)

set_remote_paths = ->
	$('.imgix-fluid').each (i, el) ->
		path = $(el).data('src')
		path = CLOUDINARY_PATH + path
		apply_img_src(el, path)

module.exports = ->
	# If we're in production
	if document.location.href.indexOf('zek') > -1
		set_remote_paths()
	else
		overwrite_images_with_local_paths()