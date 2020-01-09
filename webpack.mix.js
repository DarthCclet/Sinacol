const mix = require('laravel-mix');
require('laravel-mix-dload');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// jquery-migrate
mix.copy('node_modules/jquery-migrate/dist/', 'public/assets/plugins/jquery-migrate/dist/');

// bootstrap
mix.copy('node_modules/bootstrap/dist/', 'public/assets');


// apexcharts
mix.copy('node_modules/apexcharts/dist/', 'public/assets/plugins/apexcharts/dist/');

// flot
mix.copy('node_modules/flot/', 'public/assets/plugins/flot/');

// nvd3
mix.copy('node_modules/d3/d3.min.js', 'public/assets/plugins/d3/d3.min.js');
mix.copy('node_modules/nvd3/build/', 'public/assets/plugins/nvd3/build/');

// jvectormap-next
mix.copy('node_modules/jvectormap-next/jquery-jvectormap.css', 'public/assets/plugins/jvectormap-next/jquery-jvectormap.css');
mix.copy('node_modules/jvectormap-next/jquery-jvectormap.min.js', 'public/assets/plugins/jvectormap-next/jquery-jvectormap.min.js');

// gritter
mix.copy('node_modules/gritter/', 'public/assets/plugins/gritter/');

// bootstrap-datepicker
mix.copy('node_modules/bootstrap-datepicker/dist/', 'public/assets/plugins/bootstrap-datepicker/dist/');

// jquery-sparkline
mix.copy('node_modules/jquery-sparkline/jquery.sparkline.min.js', 'public/assets/plugins/jquery-sparkline/jquery.sparkline.min.js');

// tag-it
mix.copy('node_modules/tag-it/css/', 'public/assets/plugins/tag-it/css/');
mix.copy('node_modules/tag-it/js/', 'public/assets/plugins/tag-it/js/');

// bootstrap3-wysihtml5-bower
mix.copy('node_modules/bootstrap3-wysihtml5-bower/dist/', 'public/assets/plugins/bootstrap3-wysihtml5-bower/dist/');

// flag-icon-css
mix.copy('node_modules/flag-icon-css/', 'public/assets/plugins/flag-icon-css/');

// sweetalert
mix.copy('node_modules/sweetalert/dist/', 'public/assets/plugins/sweetalert/dist/');

// simple-line-icons
mix.copy('node_modules/simple-line-icons/', 'public/assets/plugins/simple-line-icons/');

// bootstrap-social
mix.copy('node_modules/bootstrap-social/bootstrap-social.css', 'public/assets/plugins/bootstrap-social/bootstrap-social.css');

// intro.js
mix.copy('node_modules/intro.js/minified/', 'public/assets/plugins/intro.js/minified/');

// intro.js
mix.copy('node_modules/jstree/dist/', 'public/assets/plugins/jstree/dist/');

// jquery-knob
mix.copy('node_modules/jquery-knob/', 'public/assets/plugins/jquery-knob/');

// morris
mix.copy('node_modules/raphael/raphael.min.js', 'public/assets/plugins/raphael/raphael.min.js');
mix.copy('node_modules/raphael/raphael.js', 'public/assets/plugins/raphael/raphael.js');
mix.copy('node_modules/morris.js/morris.css', 'public/assets/plugins/morris.js/morris.css');
mix.copy('node_modules/morris.js/morris.js', 'public/assets/plugins/morris.js/morris.js');
mix.copy('node_modules/morris.js/morris.min.js', 'public/assets/plugins/morris.js/morris.min.js');

// chart.js
mix.copy('node_modules/chart.js/dist/', 'public/assets/plugins/chart.js/dist/');

// fullcalendar
mix.copy('node_modules/fullcalendar/dist/', 'public/assets/plugins/fullcalendar/dist/');

// moment
mix.copy('node_modules/moment/', 'public/assets/plugins/moment/');

// lity
mix.copy('node_modules/lity/dist/', 'public/assets/plugins/lity/dist/');

// lightbox2
mix.copy('node_modules/lightbox2/dist/', 'public/assets/plugins/lightbox2/dist/');

// ckeditor
mix.copy('node_modules/ckeditor/', 'public/assets/plugins/ckeditor/');

// x-editable-bs4
mix.copy('node_modules/x-editable-bs4/dist/', 'public/assets/plugins/x-editable-bs4/dist/');

// parsleyjs
mix.copy('node_modules/parsleyjs/', 'public/assets/plugins/parsleyjs/');

// abpetkov-powerange
mix.copy('node_modules/abpetkov-powerange/dist/', 'public/assets/plugins/abpetkov-powerange/dist/');

// smartwizard
mix.copy('node_modules/smartwizard/dist/', 'public/assets/plugins/smartwizard/dist/');

// summernote
mix.copy('node_modules/summernote/dist/', 'public/assets/plugins/summernote/dist/');

// jquery-mockjax
mix.copy('node_modules/jquery-mockjax/dist/', 'public/assets/plugins/jquery-mockjax/dist/');

// select2
mix.copy('node_modules/select2/dist/', 'public/assets/plugins/select2/dist/');

// dropzone
mix.copy('node_modules/dropzone/dist/', 'public/assets/plugins/dropzone/dist/');

// blueimp-file-upload
mix.copy('node_modules/blueimp-file-upload/', 'public/assets/plugins/blueimp-file-upload/');
mix.copy('node_modules/blueimp-load-image/', 'public/assets/plugins/blueimp-load-image/');
mix.copy('node_modules/blueimp-tmpl/', 'public/assets/plugins/blueimp-tmpl/');
mix.copy('node_modules/blueimp-gallery/', 'public/assets/plugins/blueimp-gallery/');
mix.copy('node_modules/blueimp-canvas-to-blob/', 'public/assets/plugins/blueimp-canvas-to-blob/');

// ion-rangeslider
mix.copy('node_modules/ion-rangeslider/', 'public/assets/plugins/ion-rangeslider/');

// jquery.maskedinput
mix.copy('node_modules/jquery.maskedinput/', 'public/assets/plugins/jquery.maskedinput/');

// bootstrap-colorpicker
mix.copy('node_modules/bootstrap-colorpicker/', 'public/assets/plugins/bootstrap-colorpicker/');

// bootstrap-timepicker
mix.copy('node_modules/bootstrap-timepicker/', 'public/assets/plugins/bootstrap-timepicker/');

// pwstrength-bootstrap
mix.copy('node_modules/pwstrength-bootstrap/dist/', 'public/assets/plugins/pwstrength-bootstrap/dist/');

// pwstrength-bootstrap
mix.copy('node_modules/isotope-layout/dist/', 'public/assets/plugins/isotope-layout/dist/');

// bootstrap-combobox
mix.copy('node_modules/@danielfarrell/bootstrap-combobox/', 'public/assets/plugins/@danielfarrell/bootstrap-combobox/');

// bootstrap-select
mix.copy('node_modules/bootstrap-select/', 'public/assets/plugins/bootstrap-select/');

// bootstrap-daterangepicker
mix.copy('node_modules/bootstrap-daterangepicker/', 'public/assets/plugins/bootstrap-daterangepicker/');

// eonasdan-bootstrap-datetimepicker
mix.copy('node_modules/eonasdan-bootstrap-datetimepicker/', 'public/assets/plugins/eonasdan-bootstrap-datetimepicker/');

// jquery-simplecolorpicker
mix.copy('node_modules/jquery-simplecolorpicker/', 'public/assets/plugins/jquery-simplecolorpicker/');

// bootstrap-show-password
mix.copy('node_modules/bootstrap-show-password/', 'public/assets/plugins/bootstrap-show-password/');

// clipboard
mix.copy('node_modules/clipboard/', 'public/assets/plugins/clipboard/');

// datatables
mix.copy('node_modules/datatables.net/js/', 'public/assets/plugins/datatables.net/js/');
mix.copy('node_modules/datatables.net-bs4/css/', 'public/assets/plugins/datatables.net-bs4/css/');
mix.copy('node_modules/datatables.net-bs4/js/', 'public/assets/plugins/datatables.net-bs4/js/');
mix.copy('node_modules/datatables.net-autofill/js/', 'public/assets/plugins/datatables.net-autofill/js/');
mix.copy('node_modules/datatables.net-autofill-bs4/css/', 'public/assets/plugins/datatables.net-autofill-bs4/css/');
mix.copy('node_modules/datatables.net-autofill-bs4/js/', 'public/assets/plugins/datatables.net-autofill-bs4/js/');
mix.copy('node_modules/datatables.net-responsive/js/', 'public/assets/plugins/datatables.net-responsive/js/');
mix.copy('node_modules/datatables.net-responsive-bs4/css/', 'public/assets/plugins/datatables.net-responsive-bs4/css/');
mix.copy('node_modules/datatables.net-responsive-bs4/js/', 'public/assets/plugins/datatables.net-responsive-bs4/js/');
mix.copy('node_modules/datatables.net-buttons/js/', 'public/assets/plugins/datatables.net-buttons/js/');
mix.copy('node_modules/datatables.net-buttons-bs4/css/', 'public/assets/plugins/datatables.net-buttons-bs4/css/');
mix.copy('node_modules/datatables.net-buttons-bs4/js/', 'public/assets/plugins/datatables.net-buttons-bs4/js/');
mix.copy('node_modules/datatables.net-colreorder/js/', 'public/assets/plugins/datatables.net-colreorder/js/');
mix.copy('node_modules/datatables.net-colreorder-bs4/css/', 'public/assets/plugins/datatables.net-colreorder-bs4/css/');
mix.copy('node_modules/datatables.net-colreorder-bs4/js/', 'public/assets/plugins/datatables.net-colreorder-bs4/js/');
mix.copy('node_modules/datatables.net-fixedcolumns/js/', 'public/assets/plugins/datatables.net-fixedcolumns/js/');
mix.copy('node_modules/datatables.net-fixedcolumns-bs4/css/', 'public/assets/plugins/datatables.net-fixedcolumns-bs4/css/');
mix.copy('node_modules/datatables.net-fixedcolumns-bs4/js/', 'public/assets/plugins/datatables.net-fixedcolumns-bs4/js/');
mix.copy('node_modules/datatables.net-fixedheader/js/', 'public/assets/plugins/datatables.net-fixedheader/js/');
mix.copy('node_modules/datatables.net-fixedheader-bs4/css/', 'public/assets/plugins/datatables.net-fixedheader-bs4/css/');
mix.copy('node_modules/datatables.net-fixedheader-bs4/js/', 'public/assets/plugins/datatables.net-fixedheader-bs4/js/');
mix.copy('node_modules/datatables.net-keytable/js/', 'public/assets/plugins/datatables.net-keytable/js/');
mix.copy('node_modules/datatables.net-keytable-bs4/css/', 'public/assets/plugins/datatables.net-keytable-bs4/css/');
mix.copy('node_modules/datatables.net-keytable-bs4/js/', 'public/assets/plugins/datatables.net-keytable-bs4/js/');
mix.copy('node_modules/datatables.net-rowreorder/js/', 'public/assets/plugins/datatables.net-rowreorder/js/');
mix.copy('node_modules/datatables.net-rowreorder-bs4/css/', 'public/assets/plugins/datatables.net-rowreorder-bs4/css/');
mix.copy('node_modules/datatables.net-rowreorder-bs4/js/', 'public/assets/plugins/datatables.net-rowreorder-bs4/js/');
mix.copy('node_modules/datatables.net-scroller/js/', 'public/assets/plugins/datatables.net-scroller/js/');
mix.copy('node_modules/datatables.net-scroller-bs4/css/', 'public/assets/plugins/datatables.net-scroller-bs4/css/');
mix.copy('node_modules/datatables.net-scroller-bs4/js/', 'public/assets/plugins/datatables.net-scroller-bs4/js/');
mix.copy('node_modules/datatables.net-select/js/', 'public/assets/plugins/datatables.net-select/js/');
mix.copy('node_modules/datatables.net-select-bs4/css/', 'public/assets/plugins/datatables.net-select-bs4/css/');
mix.copy('node_modules/datatables.net-select-bs4/js/', 'public/assets/plugins/datatables.net-select-bs4/js/');
mix.copy('node_modules/pdfmake/build/', 'public/assets/plugins/pdfmake/build/');
mix.copy('node_modules/jszip/dist/', 'public/assets/plugins/jszip/dist/');

// plugins download from url
mix.download({
	enabled: true,
	urls: [{
		'url': 'https://raw.githubusercontent.com/highlightjs/cdn-release/master/build/highlight.min.js',
		'dest': 'public/assets/plugins/highlight.js/'
	},{
		'url': 'https://raw.githubusercontent.com/abpetkov/switchery/master/dist/switchery.min.css',
		'dest': 'public/assets/plugins/switchery/'
	}, {
		'url': 'https://raw.githubusercontent.com/abpetkov/switchery/master/dist/switchery.min.js',
		'dest': 'public/assets/plugins/switchery/'
	}, {
		'url': 'https://raw.githubusercontent.com/kbwood/countdown/master/dist/js/jquery.plugin.min.js',
		'dest': 'public/assets/plugins/countdown/'
	}, {
		'url': 'https://raw.githubusercontent.com/kbwood/countdown/master/dist/js/jquery.countdown.min.js',
		'dest': 'public/assets/plugins/countdown/'
	}, {
		'url': 'https://raw.githubusercontent.com/kbwood/countdown/master/dist/css/jquery.countdown.css',
		'dest': 'public/assets/plugins/countdown/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/js/jquery.superbox.min.js',
		'dest': 'public/assets/plugins/superbox/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/css/superbox.min.css',
		'dest': 'public/assets/plugins/superbox/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/css/font/superboxicons.eot',
		'dest': 'public/assets/plugins/superbox/font/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/css/font/superboxicons.svg',
		'dest': 'public/assets/plugins/superbox/font/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/css/font/superboxicons.ttf',
		'dest': 'public/assets/plugins/superbox/font/'
	}, {
		'url': 'https://raw.githubusercontent.com/seyDoggy/superbox/master/css/font/superboxicons.woff',
		'dest': 'public/assets/plugins/superbox/font/'
	}, {
		'url': 'http://jvectormap.com/js/jquery-jvectormap-world-mill.js',
		'dest': 'public/assets/plugins/jvectormap-next/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/css/ionicons.min.css',
		'dest': 'public/assets/plugins/ionicons/css/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/fonts/ionicons.eot',
		'dest': 'public/assets/plugins/ionicons/fonts/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/fonts/ionicons.woff2',
		'dest': 'public/assets/plugins/ionicons/fonts/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/fonts/ionicons.woff',
		'dest': 'public/assets/plugins/ionicons/fonts/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/fonts/ionicons.ttf',
		'dest': 'public/assets/plugins/ionicons/fonts/'
	}, {
		'url': 'https://unpkg.com/ionicons@4.2.6/dist/fonts/ionicons.svg',
		'dest': 'public/assets/plugins/ionicons/fonts/'
	}, {
		'url': 'http://lab.xero.nu/bootstrap_calendar/lib/css/bootstrap_calendar.css',
		'dest': 'public/assets/plugins/bootstrap-calendar/css/'
	}, {
		'url': 'http://lab.xero.nu/bootstrap_calendar/lib/js/bootstrap_calendar.min.js',
		'dest': 'public/assets/plugins/bootstrap-calendar/js/'
	}, {
		'url': 'https://raw.githubusercontent.com/extremeFE/bootstrap-colorpalette/master/css/bootstrap-colorpalette.css',
		'dest': 'public/assets/plugins/bootstrap-colorpalette/css/'
	}, {
		'url': 'https://raw.githubusercontent.com/extremeFE/bootstrap-colorpalette/master/js/bootstrap-colorpalette.js',
		'dest': 'public/assets/plugins/bootstrap-colorpalette/js/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/css/bootstrap-datetimepicker.min.css',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/css/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/bootstrap-datetimepicker.min.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ar.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.az.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.bg.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.bn.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ca.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.cs.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.da.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.de.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ee.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.el.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.es.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.fi.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.fr.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.he.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.hr.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.hu.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.hy.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.id.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.is.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.it.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ja.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ka.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ko.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.lt.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.lv.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ms.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.nb.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.nl.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.no.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.pl.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.pt-BR.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.pt.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ro.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.rs-latin.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.rs.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ru.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.sk.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.sl.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.sv.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.sw.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.th.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.tr.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.ua.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.uk.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.zh-CN.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}, {
		'url': 'https://raw.githubusercontent.com/smalot/bootstrap-datetimepicker/master/js/locales/bootstrap-datetimepicker.zh-TW.js',
		'dest': 'public/assets/plugins/bootstrap-datetimepicker/js/locales/'
	}]
});


