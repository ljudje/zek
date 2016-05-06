$                = require('jquery')
foundation       = require('foundation')

responsive_img   = require('./responsive_img')
equalize_heights = require('./equalize_heights')
keyboard_nav     = require('./keyboard_nav')
external_links   = require('./external_links')

landing_page     = require('./landing_page')
project_page     = require('./project_page')

$(document).foundation()

$(document).ready ->
	responsive_img()
	equalize_heights()
	landing_page()
	project_page()
	keyboard_nav()
	external_links()