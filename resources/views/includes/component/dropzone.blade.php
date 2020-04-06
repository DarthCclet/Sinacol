@push('css')
    <!--Librerias de date dropzone-->
    <!--<link href="/assets/plugins/dropzone/dist/min/dropzone.min.css" rel="stylesheet" />-->
    <!--<link href="/assets/plugins/lightbox2/dist/css/lightbox.css" rel="stylesheet" />-->
    <link href="/assets/plugins/ekko-lightbox/ekko-lightbox.css" rel="stylesheet" />
    <link href="/assets/plugins/blueimp-file-upload/css/jquery.fileupload.css" rel="stylesheet" />
    <link href="/assets/plugins/blueimp-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />
    <style type="text/css">
         .center-block-horiz {
            margin-left: auto !important;
            margin-right: auto !important;
          }
         .set-margin-cicis-menu-to-go {
           margin: 20px;
         }
         .set-padding-cicis-menu-to-go {
           padding: 20px;
         }
         .set-border-cicis-menu-to-go {
            border: 5px inset #4f4f4f;
         }
         set-box-shadow-cicis-menu-to-go {
           -webkit-box-shadow: 4px 4px 14px #4f4f4f;
              -moz-box-shadow: 4px 4px 14px #4f4f4f;
                   box-shadow: 4px 4px 14px #4f4f4f;
         }
         .responsive-wrapper {
           position: relative;
           height: 0;    /* gets height from padding-bottom */
           overflow: hidden;
           /* put following styles (necessary for overflow and 
              scrolling handling) inline in .embed-responsive-element-wrapper around iframe because not stable in CSS
             -webkit-overflow-scrolling: touch; 
                               overflow: auto; */
         }
         .responsive-wrapper img,
         .responsive-wrapper object,
         .responsive-wrapper iframe {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;

           border-style: none;
           padding: 0;
           margin: 0;
         }

         /*
             css particular to this iframe
         */

         #Iframe-Cicis-Menu-To-Go {
           max-width: 666.67px;
           max-height: 600px;
           overflow: hidden;
         }
         /*
            padding-bottom = h/w as %
         */
         .responsive-wrapper-padding-bottom-90pct {
           padding-bottom: 90%;
         }
    </style>
@endpush

@push('scripts')
    <!--Librerias de dropzone-->
    <!--<script src="/assets/plugins/dropzone/dist/min/dropzone.min.js"></script>-->
    <!--<script src="/assets/plugins/highlight.js/highlight.min.js"></script>-->
    <!--<script src="/assets/js/demo/render.highlight.js"></script>-->
    <script src="/assets/plugins/blueimp-file-upload/js/vendor/jquery.ui.widget.js"></script>
    <script src="/assets/plugins/blueimp-tmpl/js/tmpl.js"></script>
    <script src="/assets/plugins/blueimp-load-image/js/load-image.all.min.js"></script>
    <script src="/assets/plugins/blueimp-canvas-to-blob/js/canvas-to-blob.js"></script>
    <script src="/assets/plugins/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.iframe-transport.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-image.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-audio.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-video.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>
    <script src="/assets/plugins/blueimp-file-upload/js/jquery.fileupload-ui.js"></script>
    <!--[if (gte IE 8)&(lt IE 10)]>
            <script src="/assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>
    <![endif]-->
    <script src="/assets/js/demo/form-multiple-upload.demo.js"></script>
    <script src="/assets/plugins/isotope-layout/dist/isotope.pkgd.min.js"></script>
	<!--<script src="/assets/plugins/lightbox2/dist/js/lightbox.min.js"></script>-->
	<script src="/assets/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
	<!--<script src="/assets/js/demo/gallery.demo.js"></script>-->
@endpush