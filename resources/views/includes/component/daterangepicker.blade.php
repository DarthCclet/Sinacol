@push('css')
    <!--Librerias de date pickers-->
    <link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
    <link href="/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" />
    <link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
    <link href="/assets/plugins/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <!--Librerias de switch-->
    <link href="/assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="/assets/plugins/abpetkov-powerange/dist/powerange.min.css" rel="stylesheet" />

    <!--Librerias select2-->
    <link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/assets/plugins/smartwizard/dist/css/smart_wizard.css" rel="stylesheet" />

    <!--Librerias wysihtml5-->
    <link href="/assets/plugins/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.min.css" rel="stylesheet" />

    <link href="/assets/css/default/ui-datepicker.css" rel="stylesheet" />
@endpush

@push('scripts')
    <!--Librerias de date pickers-->
    <script src="/assets/plugins/moment/moment.js"></script>
    <script src="/assets/plugins/moment/locale/es.js"></script>

    <script src="/assets/plugins/jquery-ui/core.min.js"></script>


    <script src="/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
    <script src="/assets/plugins/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!--Librerias de switch-->
    <script src="/assets/plugins/switchery/switchery.min.js"></script>
    <script src="/assets/plugins/abpetkov-powerange/dist/powerange.min.js"></script>
    <script src="/assets/js/demo/form-slider-switcher.demo.js"></script>

    <!--Librerias select2-->
    <script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
    <script src="/assets/plugins/select2/dist/js/i18n/es.js"></script>
    <!-- Libreria Wizard -->
    <script src="/assets/plugins/smartwizard/dist/js/jquery.smartWizard.js"></script>
    <!--<script src="/assets/js/demo/form-wizards.demo.js"></script>-->
    <script src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
    <script src="/assets/plugins/parsleyjs/dist/i18n/es.js"></script>

    <!--Librerias wysihtml5-->
    <script src="/assets/plugins/ckeditor/ckeditor.js"></script>
    <script src="/assets/plugins/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="/assets/plugins/bootstrap3-wysihtml5-bower/dist/locales\bootstrap-wysihtml5.es-ES.js"></script>
    <script src="/assets/js/demo/form-wysiwyg.demo.js"></script>

    <script>
        $(document).ready(function() {
            $.datepicker.regional.es = {
                closeText: 'Cerrar',
                prevText: 'Ant',
                nextText: 'Sig',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mi&eacute;', 'Juv', 'Vie', 'S&aacute;b'],
                dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'S&aacute;b'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional.es);
        });
    </script>
@endpush
