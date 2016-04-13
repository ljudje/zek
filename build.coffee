# This build script composes markdown files into HTML with the help of Metalsmith

# Requirements

Metalsmith =        require('metalsmith')
Handlebars =        require('handlebars')
metadata =          require('metalsmith-metadata')
markdown =          require('metalsmith-markdown')
collections =       require('metalsmith-collections')
layouts =           require('metalsmith-layouts')
in_place =          require('metalsmith-in-place')
paths =             require('metalsmith-paths')
permalinks =        require('metalsmith-permalinks')
fs =                require('fs')
utils =             require('./src/builder/utils')
helpers =           require('./src/builder/helpers')

# Dumps entire metalsmith.metadata() to console
debug_metadata = utils.debug_metadata
# Decorates files with appropriate .template according to pattern
assign_layouts = utils.assign_layouts
# Remove images from scope
remove_images = utils.remove_images_from_scope
# Remove drafts
remove_drafts = utils.remove_drafts
# Remove ignored
remove_ignored = utils.remove_ignored_files

# A Handlebars helper that helps debug the local scope
Handlebars.registerHelper "debug", helpers.debug_handlebars
# Template helpers
Handlebars.registerHelper "project_image", helpers.project_image_hb
Handlebars.registerHelper "project_image_url", helpers.project_image_url
Handlebars.registerHelper "doc_image", helpers.doc_image_hb
Handlebars.registerHelper "doc_image_url", helpers.doc_image_url


# TODO: use minimatch globbing in all patterns & DRY
project_pattern = 'projects/([a-z]|-|[0-9])+.(txt|md)'
person_pattern = 'members/([a-z]|-)+.md'
# image_pattern = 'projects\/.+\/.+\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)';

# BUILD SCRIPT
metalsmith = Metalsmith(__dirname)
	# Base folder config
	.source('content').destination('build').clean(false)
	# Global site metadata, accessible as `site` in handlebars templates
	.use(metadata(
		site: 'site.yaml'
	))
	# Decorate every collection item with extensible paths info
	.use(paths(
		property: 'paths'
	))
	# Removes drafts & ignored files from the pipeline
	.use(remove_drafts()).use(remove_ignored())
	# Collections of peole and projects
	.use(collections(
		people:
			pattern: 'members/*.md'
		projects:
			pattern: 'projects/*.md'
	))
	# Removes images from the content compilation pipeline
	.use(remove_images())
	# Assign layouts to markdown files according to pattern (source in util.js)
	.use(assign_layouts(
		homepage:
			layout: 'home.hbt'
			pattern: 'index.md'
		member:
			layout: 'member.hbt'
			pattern: member_pattern
		project:
			layout: 'project.hbt'
			collection: 'projects'
	))
	# Evaluate handlebars partials within markdown files
	.use(in_place(
		engine: 'handlebars'
		partials: 'src/templates/compiled/partials'
	))
	# Convert markdown to HTML
	.use(markdown())
	# Use handlebars layouts (compiled from HAML files)
	.use(layouts(
		engine: 'handlebars'
		directory: 'src/templates/compiled'
		partials: 'src/templates/compiled/partials'
	))
	# Moves HTML files into a folder with the same name, renames them to index.html
	.use(permalinks())
	# Development utility (source in util.js)
	# .use(debug_metadata())
	# Build
	.build((err) ->
		if err
			console.log err
		else
			console.log('Built HTML files from Markdown and Handlebars')
			console.log("1 Homepage")
			console.log("#{this._metadata.members.length} Members")
			console.log("#{this._metadata.projects.length} Projects")
		return
	)