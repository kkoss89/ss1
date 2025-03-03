
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
    <!-- jQuery and jQuery UI (REQUIRED) -->
    <script src="<?php echo BASE; ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE; ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- elFinder JS (REQUIRED) -->
    <script src="<?php echo BASE; ?>assets/plugins/elFinder/js/elfinder.min.js?v=2.4.0"></script>
    <script>
        if (typeof (jQuery) === 'undefined' && !window.deferAfterjQueryLoaded) {
            window.deferAfterjQueryLoaded = [];
            Object.defineProperty(window, "$", {
                set: function (value) {
                    window.setTimeout(function () {
                        $.each(window.deferAfterjQueryLoaded, function (index, fn) {
                            fn();
                        });
                    }, 0);
                    Object.defineProperty(window, "$", {
                        value: value
                    });
                },
                configurable: true
            });
        }

        var csrfData = {
            "formatted":{"token":"<?php echo strip_tags($this->security->get_csrf_hash()); ?>"},
            "token_name":"token","hash":"<?php echo strip_tags($this->security->get_csrf_hash()); ?>"
            };

        if (typeof(jQuery) == 'undefined') {
            window.deferAfterjQueryLoaded.push(function () {
                csrf_jquery_ajax_setup();
            });
            window.addEventListener('load',function(){
                csrf_jquery_ajax_setup();
            }, true);
        } else {
            csrf_jquery_ajax_setup();
        }

        function csrf_jquery_ajax_setup() {
            $.ajaxSetup({
                data: csrfData.formatted
            });
        }
    </script>
    <script>
        var BASE = '<?php echo BASE; ?>';
        var FileBrowserDialogue = {
            init: function() {},
            mySubmit: function(URL) {
                // pass selected file path to TinyMCE
                parent.tinymce.activeEditor.windowManager.getParams().setUrl(URL.url);
                // force the TinyMCE dialog to refresh and fill in the image dimensions
                var t = parent.tinymce.activeEditor.windowManager.windows[0];
                t.find('#src').fire('change');
                // close popup window
                parent.tinymce.activeEditor.windowManager.close();
            }
        }
    </script>
</head>
<body>
    <div>
        <div id="elfinder"></div>
    </div>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE; ?>assets/plugins/elFinder/css/theme-bootstrap.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"></script>
    <script>
        define('elFinderConfig', {
         defaultOpts: {
             onlyMimes: ['image','video'],
             url: '<?php echo strip_tags($connector); ?>', 
             commandsOptions: {
                 edit: {
                     extraOptions: {
                         creativeCloudApiKey: '',
                         managerUrl: ''
                     }
                 },
                 quicklook: {
                     // to enable preview with Google Docs Viewer
                     googleDocsMimes: ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                 }
             }
             ,
             bootCallback: function(fm, extraObj) {
                 fm.bind('init', function() {
                 });
                 var title = document.title;
                 fm.bind('open', function() {
                     var path = '',
                         cwd = fm.cwd();
                     if (cwd) {
                         path = fm.path(cwd.hash) || null;
                     }
                     document.title = path ? path + ':' + title : title;
                 }).bind('destroy', function() {
                     document.title = title;
                 });
             }
         },
         managers: {
             'elfinder': {}
         }
     });
     define('returnVoid', void 0);
     (function() {
         var // elFinder version
             elver = '2.1.49',
             // jQuery and jQueryUI version
             jqver = '3.2.1',
             uiver = '1.12.1',
             // Start elFinder (REQUIRED)
             start = function(elFinder, editors, config) {
                 // load jQueryUI CSS
                 elFinder.prototype.loadCss('//cdnjs.cloudflare.com/ajax/libs/jqueryui/' + uiver + '/themes/smoothness/jquery-ui.css');

                 $(function() {
                     var elfEditorCustomData = {};
                     if (typeof(csrfData) !== 'undefined') {
                         elfEditorCustomData[csrfData['token_name']] = csrfData['hash'];
                     }
                     var optEditors = {
                             commandsOptions: {
                                 edit: {
                                     editors: Array.isArray(editors) ? editors : []
                                 }
                             }
                         },
                         opts = {
                             height: 700,
                             customData: elfEditorCustomData,
                             getFileCallback: function(file, fm) {
                                FileBrowserDialogue.mySubmit(file);
                             },
                            contextmenu : {
                                  files  : [
                                    'getfile', '|','open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
                                    'rm', '|', 'edit', 'rename', '|', 'archive', 'extract'
                                  ]
                             },
                             ui: ['toolbar', 'tree', 'path', 'stat'],
                             uiOptions: {
                                 // toolbar configuration
                                 toolbar: [
                                     ['back', 'forward'],
                                     ['mkdir', 'mkfile', 'upload'],
                                     ['open', 'download', 'getfile'],
                                     ['quicklook'],
                                     ['copy', 'paste'],
                                     ['rm'],
                                     ['duplicate', 'rename', 'edit'],
                                     ['extract', 'archive'],
                                     ['search'],
                                     ['view'],
                                     ['info'],
                                 ]
                             }
                         };

                     // Interpretation of "elFinderConfig"
                     if (config && config.managers) {
                         $.each(config.managers, function(id, mOpts) {
                             opts = Object.assign(opts, config.defaultOpts || {});
                             // editors marges to opts.commandOptions.edit
                             try {
                                 mOpts.commandsOptions.edit.editors = mOpts.commandsOptions.edit.editors.concat(editors || []);
                             } catch (e) {
                                 Object.assign(mOpts, optEditors);
                             }
                             // Make elFinder
                             $('#' + id).elfinder(
                                 // 1st Arg - options
                                 $.extend(true, {
                                     lang: 'en'
                                 }, opts, mOpts || {}),
                                 // 2nd Arg - before boot up function
                                 function(fm, extraObj) {
                                     // `init` event callback function
                                     fm.bind('init', function() {

                                     });
                                 }
                             );
                         });
                     } else {
                         console.error('"elFinderConfig" object is wrong.');
                     }
                 });
             },

             // JavaScript loader (REQUIRED)
             load = function() {
                 require(
                     [
                         'elfinder', 'extras/editors.default' // load text, image editors
                         , 'elFinderConfig'
                         //  , 'extras/quicklook.googledocs'  // optional preview for GoogleApps contents on the GoogleDrive volume
                     ],
                     start,
                     function(error) {
                         alert(error.message);
                     }
                 );
             },

             // is IE8? for determine the jQuery version to use (optional)
             ie8 = (typeof window.addEventListener === 'undefined' && typeof document.getElementsByClassName === 'undefined');

         // config of RequireJS (REQUIRED)
         require.config({
             baseUrl: BASE + 'assets/plugins/elFinder/js',
             paths: {
                 'jquery': '//cdnjs.cloudflare.com/ajax/libs/jquery/' + (ie8 ? '1.12.4' : jqver) + '/jquery.min',
                 'jquery-ui': '//cdnjs.cloudflare.com/ajax/libs/jqueryui/' + uiver + '/jquery-ui.min',
                 'elfinder': 'elfinder.min',
             },
             waitSeconds: 10 // optional
         });

         // load JavaScripts (REQUIRED)
         load();

     })();
    </script>
</body>
</html>
