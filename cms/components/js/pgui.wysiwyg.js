require.config ({
    shim: {
        '_custom/lib/ckeditor/adapters/jquery.js' : {
            deps: ['../ckeditor.js']
        }
    }
});

define(function(require, exports) {

    var Class   = require('class'),
        events  = require('microevent');

    require('_custom/lib/ckeditor/adapters/jquery.js');


    var toolbars = {
        'default': [

            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote', '-','JustifyLeft','JustifyCenter','JustifyRight' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor','-','Source' ] },
            '/',
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
            { name: 'insert', items : [ 'Image','Templates2','Table','HorizontalRule','SpecialChar', 'Format', 'Styles', 'Maximize' ] },
        ],
        'full': [
            { name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
            { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
            '/',
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv', '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
            '/',
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
        ],
        'basic': [
            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
        ]
    };

    CKEDITOR.stylesSet.add( 'my_styles', [
        // Block-level styles.
        //{ name: 'Izpostavitev', element: 'p', attributes: { 'class': 'excerpt' } },
        //{ name: 'Red Title',  element: 'h3', styles: { color: 'Red' } },

        // Inline styles.
        //{ name: 'CSS Style', element: 'span', attributes: { 'class': 'my_style' } },
        //{ name: 'Marker: Yellow', element: 'span', styles: { 'background-color': 'Yellow' } }
    ]);

    var WYSISYGEditor = exports.WYSISYGEditor = Class.extend({
        init: function($container) {

            this.$container = $container;
            this.$container.data('pgui-class', this);
            var self = this;

            this.$container.ckeditor({
				filebrowserBrowseUrl : '_custom/lib/elFinder/elfinder.html',
                toolbar: toolbars['default'],
				width: '800px',
				height: '400px',
				language: 'sl',
                //skin: 'minimalist',
				entities_greek: false,
				entities: false,
				basicEntities: false,
				forcePasteAsPlainText: true,
                extraPlugins: 'lineutils,widget,image2',
                removePlugins: 'image,forms',
                disallowedContent: 'img[width,height]',
                allowedContent: true,
                stylesSet: 'my_styles'
            });

			CKEDITOR.on( 'dialogDefinition', function( ev )
			{
				// Take the dialog name and its definition from the event data.
				var dialogName = ev.data.name;
				var dialogDefinition = ev.data.definition;

				if ( dialogName == 'table' ) {
					// Get a reference to the "Table Info" tab.
					var infoTab = dialogDefinition.getContents( 'info' );
					txtWidth = infoTab.get( 'txtWidth' );
					txtWidth['default'] = '100%';
					infoTab.remove( 'txtHeight');
					infoTab.remove( 'txtSummary');
					infoTab.remove( 'txtCaption');
					infoTab.remove( 'cmbAlign');
					infoTab.remove( 'txtBorder');
					infoTab.remove( 'selHeaders');
					infoTab.remove( 'txtCellSpace');
					infoTab.remove( 'txtCellPad');

					var advancedTab = dialogDefinition.getContents ('advanced');
					txtClass = advancedTab.get ('advCSSClasses');
					txtClass['default'] = 'table';
				}
			});

            this.$container.ckeditorGet().on("instanceReady", function(){
                this.document.on("keyup", function(){
                    self.$container.html(self.$container.val());
                });
            });

            this.$container.data('editor-class', this);
        },

        onChange: function(callback) {
            this.bind('changed', callback);
        },

        destroy: function() {
            this.$container.ckeditorGet().destroy();
        }

    });
    events.mixin(WYSISYGEditor);

});
