var Metalsmith     = require('metalsmith'),
    Handlebars     = require('handlebars'),
    metadata       = require('metalsmith-metadata'),
    markdown       = require('metalsmith-markdown'),
    collections    = require('metalsmith-collections'),
    layouts        = require('metalsmith-layouts'),
    in_place       = require('metalsmith-in-place'),
    paths          = require('metalsmith-paths'),
    permalinks     = require('metalsmith-permalinks'),
    assets         = require('metalsmith-assets'),
    watch          = require('metalsmith-watch'),
    connect_reload = require('connect-livereload'),
    connect        = require('connect'),
    serve_static   = require('serve-static'),
    http           = require('http'),
    fs             = require('fs'),
    utils          = require('./utils');

// Dumps entire metalsmith.metadata() to console
var debug_metadata = utils.debug_metadata;

// Decorates files with appropriate .template according to pattern
var assign_layouts = utils.assign_layouts;

// Load partials to be used in templates: {{ > image }}
Handlebars.registerPartial('header', fs.readFileSync(__dirname + '/src/templates/partials/header.hbt').toString());
Handlebars.registerPartial('footer', fs.readFileSync(__dirname + '/src/templates/partials/footer.hbt').toString());
// Handlebars.registerPartial('image', fs.readFileSync(__dirname + '/src/templates/image.hbt').toString());

// TODO: use minimatch globbing in all patterns & DRY
var project_pattern = 'projects\/([a-z]|-|[0-9])+\.(txt|md)';
var member_pattern = 'members\/([a-z]|-)+\.md';
// var image_pattern = 'projects\/.+\/.+\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)';

// ### BUILD SCRIPT ### //

var metalsmith = Metalsmith(__dirname)

  .source('content')
  .destination('build')

  // Copy assets
  .use(assets({source: './src/assets', destination: './assets'}))

  // .use(watch({
  //   paths: {
  //     "${source}/**/*": true,   // every changed files will trigger a rebuild of themselves
  //     "templates/**/*": "**/*" // every templates changed will trigger a rebuild of all files
  //   },
  //   livereload: true
  // }))

  .use(metadata({site: 'site.yaml'}))

  // Load projects, images & their descriptions as collections
  .use(collections({
    members: {                 pattern: 'members/*.md' },
    projects: {                pattern: 'projects/*.md' } //,
    // images: {               pattern: 'projects/*/*.png' },
    // image_descriptions: {   pattern: 'projects/*/*.{txt,md,HTML}' }
  }))

  // Adds .extname, .href, .base, etc. to a files .paths property
  .use(paths({property: 'paths'}))
  
  // Assign Handlebars layouts according to matching patterns
  .use(assign_layouts({
    homepage:    {layout: 'home.hbt',     pattern: 'index\.md'},
    member:     {layout: 'member.hbt', pattern: member_pattern},
    project:    {layout: 'project.hbt', collection: 'projects'}
  }))
  
  // Links projects/*/*.txt descriptions to images with the same .name
  //.use(image_description_linker())  
  // Links images contained in projects/*/ folder to the project with the same .name
  //.use(project_image_linker())

  // Evaluate handlebars templates & partials in place (in .md and .hbs files)
  .use(in_place({engine: 'handlebars', partials: 'src/templates/partials'}))

  // Converts .md files to HTML  
  .use(markdown())

  // Render Handlebars templates (home, project) on files. Images are just copied over tho!
  .use(layouts({engine: 'handlebars', directory: 'src/templates', partials: 'src/templates/partials'}))

  // Permalinks
  .use(permalinks())
  
  // Debugging
  .use(debug_metadata())

  .build(function (err) { if(err) console.log(err) });

// ### DEV SERVER ### //

// // Config
// var liverelad_port = 35729,
//     server_port = 9080;

// var app = connect()
  
//   // Live reload
//   .use(connect_reload({port: liverelad_port}))
  
//   // Static server
//   .use(serve_static(__dirname+'/build'));

// http.createServer(app).listen(server_port);