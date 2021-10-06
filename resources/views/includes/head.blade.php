<meta charset="utf-8" />
<title>Conciliación | @yield('title')</title>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
<meta content="" name="description" />
<meta content="" name="author" />
@stack('analytics')
<!-- ================== BEGIN BASE CSS STYLE ================== -->
<link href="/assets/css/default/app.min.css" rel="stylesheet" />
<!-- ================== END BASE CSS STYLE ================== -->
@stack('css')
<style>
    @if( strpos(env('APP_URL'), 'lxl') )
    body {
        background-color: #ffb3b3;
        background-image: url("/assets/img/logo/sistemadeprueba.svg");
        background-size: 200px;
        background-repeat: repeat;
    }
    @endif
    .sw-main.sw-theme-default .step-anchor > li.active > a small {
        color: #B38E5D;
    }
    .sw-main.sw-theme-default .step-anchor > li.active{
        background: {{config('colores.btn-primary-color')}} !important
    }
    .wizard-steps li.active::after, .wizard-steps li.active::before, .wizard-steps li.current::after, .wizard-steps li.current::before, .wizard-steps li.success::after, .wizard-steps li.success::before {
        background: {{config('colores.btn-primary-color')}} !important
    }
    .sw-theme-default > ul.step-anchor > li.clickable > a:hover {
        color: white !important;
        background: transparent !important;
        cursor: pointer;
    }
    .sw-theme-default > ul.step-anchor > li > a::after {
        content: "";
        background: #B38E5D;
        height: 2px;
        position: absolute;
        width: 100%;
        left: 0px;
        bottom: 0px;
        transition: all 250ms ease 0s;
        transform: scale(0);
    }
    .sw-theme-default > ul.step-anchor > li.clickable > a:hover {
        color: black !important;
        background: transparent !important;
        cursor: pointer;
    }
    .sw-theme-default > ul.step-anchor > li.active > a:hover {
        color: white !important;
        background: transparent !important;
        cursor: pointer;
    }
    .loading {
        z-index: 9999 !important;
        position: fixed;
        top: 0;
        left:-5px;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0,0,0,0.4);
    }
    .progress-content {
        position: absolute;
        width: 20%;
        height: 30px;
        top: 40%;
        left:40%;
    }

    .loading-content {
        position: absolute;
        border: 3px solid #f3f3f3; /* Light grey */
        border-top: 5px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        top: 50%;
        left:50%;
        animation: spin 2s linear infinite;
    }

    .wizard-steps li.completed,
    .wizard-steps li.current,
    .wizard-steps-extensive li.active,
    .wizard-steps-extensive li.current {
        background-color: {{config('colores.btn-primary-color')}} !important;
        color: #fff
    }

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

</style>

@if(config('colores.default') === 'NO')
    <style>
        .header {background-color: {{config('colores.encabezado-color-fondo')}} !important;}
        .btn-primary {
            color: {{config('colores.btn-primary-color')}} !important;
            background-color: {{config('colores.btn-primary-background-color')}} !important;
            border-color: {{config('colores.btn-primary-border-color')}} !important;
            box-shadow: {{config('colores.btn-primary-shadow-color')}} !important;
        }
        .btn-primary:hover {
            color: {{config('colores.btn-primary-hover-color')}} !important;
            background-color: {{config('colores.btn-primary-hover-background-color')}} !important;
            border-color: {{config('colores.btn-primary-hover-border-color')}} !important;
        }
    </style>
@endif
