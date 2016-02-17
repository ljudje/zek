pgui.wysiwyg.js

this.$container.ckeditor({
	filebrowserBrowseUrl : '_custom/lib/elFinder/elfinder.html', // eg. 'includes/elFinder/elfinder.html'
	toolbar: toolbars['default'],
	width: '800px',
	height: '400px',
	// skin: 'BootstrapCK-Skin',
	entities_greek: false
});


 'default': [

            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
			{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            '/',

            { name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar' ] },
            { name: 'styles', items : [ , 'Format' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About','-','Source' ] }

        ],
