var logging = false;

var _LOG_ = function(a, b, c, d) {
  if (logging == true) {
    console.log(a, b || '', c || '', d || '');
  }
};

var assign_layouts_by_pattern = function(config, files) {

    _LOG_('file name pattern matcher');

    var pattern = new RegExp(config.pattern);

    for (var file in files) {

      if (pattern.test(file)) {

        _LOG_('# Found matching layout', files[file].paths.name, config.pattern)
        
        var _f = files[file];
        if (!_f.layout) {
          _f.layout = config.layout;
        };

      };

    };

  },

  assign_layouts_by_collection = function(config, files) {

    _LOG_('collection matcher');

    for (var file in files) {

      if (files[file].collection.indexOf(config.collection) > -1) {

        _LOG_('# Found matching layout', files[file].paths.name, config.collection)
        
        var _f = files[file];
        if (!_f.layout) {
          _f.layout = config.layout;
        };

      };
    };
};

module.exports = {

  image_description_linker: function(config) {
    return function(files, metalsmith, done) {

      var store = {images: []};

      for (var file in files) {
        var f = files[file];
        if (f.collection == 'image_descriptions') {
          _LOG_('# Found image description', f.paths.name);
          store.images[f.paths.name] = f.contents;
        };
      };

      for (var file in files) {
        var f = files[file];
        if (f.collection == 'images') {
          _LOG_('# Found image', f.paths.name);
          if (store.images[f.paths.name] != undefined) {
            f.description = store.images[f.paths.name];
          }
        }
      };

      done();
    }
  },

  remove_drafts: function(config) {
    return function(files, metalsmith, done) {
      for (var file in files) {
        if (files[file].draft == true) {
          _LOG_('# Deleting image', files[file].name)
          delete files[file];
        }
      };
      done();
    };
  },

  remove_images_from_scope: function(config) {
    return function(files, metalsmith, done) {
      var image_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
      for (var file in files) {
        var current_extension = files[file].paths.ext.toLowerCase();
        if (image_extensions.indexOf(current_extension) > -1) {
          delete files[file];
        }
      };
      done();
    };
  },

  remove_ignored_files: function(config) {
    return function(files, metalsmith, done) {
      var ignored_files = ['.DS_Store'];
      for (var file in files) {
        var current_file = files[file].paths.base;
        if (ignored_files.indexOf(current_file) > -1) {
          delete files[file];
        }
      };
      done();
    };
  },

  project_image_linker: function(config) {
      return function(files, metalsmith, done) {

          var store = {projects: []}

          var images_for = function(project_name) {
            ensure_project(project_name)
            return store.projects[project_name].images
          }

          var ensure_project = function(project_name) {
            if (store.projects[project_name] == undefined) {
              store.projects[project_name] = {images: []}
            }
          }

          var add_image = function(project_name, image) {
            ensure_project(project_name)
            store.projects[project_name].images.push(image)
          }

          for (var file in files) {
            var _f = files[file];
            if (_f.collection == 'images') {
              var project_name = _f.paths.dir;
              var last_index = project_name.lastIndexOf('/') + 1;
              var length = project_name.length;
              project_name = project_name.substring(last_index, length);
              _f.project_name = project_name;
              add_image(project_name, _f)

              _LOG_('# Found project image', _f.paths.name)
            }
          };
          
          for (var file in files) {
            var _f = files[file];
            if (_f.collection == 'projects') {
              var project_name = _f.paths.name;
              ensure_project(project_name);
              _f.images = images_for(project_name);

              _LOG_('# Found project', _f.paths.name);
            };
          }
          done();
      };
  },

  rename_text_files: function(config) {
    return function(files, metalsmith, done) {
      for (var file in files) {
        var _f = files[file];
        if (_f.paths.ext == '.txt') {
          _f.text_file = true;
          // move file
          if (_f.collection == 'projects') {
            var renamedEntry = _f.paths.dir + '/' + _f.paths.name + '.html';
            files[renamedEntry] = files[file];
            delete files[file];
          }
        }
        if (_f.paths.ext == '.md') {
          _f.markdown_file = true;
        }
      }
      done();
    }
  },

  remove_projects: function() {
    return function(files, metalsmith, done) {
      for (var file in files) {
        var f = files[file];
        if (f.collection == 'projects') {
          delete files[file];
          _LOG_('# Deleted project')
        }
      }
    }
  },

  find_template: function(config) {
      var pattern = new RegExp(config.pattern);

      return function(files, metalsmith, done) {
          for (var file in files) {
              if (pattern.test(file)) {
                  var _f = files[file];
                  if (!_f.template) {
                      _f.template = config.template;
                  }
              }
          }
          done();
      };
  },

  assign_layouts: function(options) {
    _LOG_(options);

    return function(files, metalsmith, done) {

      for (var i in options) {

        var config = options[i];

        _LOG_('looking at', config)

        if (config.collection != undefined) {
          assign_layouts_by_collection(config, files)
        };

        if (config.pattern != undefined) {
          assign_layouts_by_pattern(config, files)
        }

      }

      done();
    }
  },

  debug_metadata: function() {
    return function(files, metalsmith, done) {

      var mdata = metalsmith.metadata();

      _LOG_('# Site metadata:');
      _LOG_(mdata);

      done();
    }
  }

}