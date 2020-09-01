<meta charset="utf-8" />
<title>Conciliación | @yield('title')</title>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
<meta content="" name="description" />
<meta content="" name="author" />

<!-- ================== BEGIN BASE CSS STYLE ================== -->
<link href="/assets/css/default/app.min.css" rel="stylesheet" />
<!-- ================== END BASE CSS STYLE ================== -->

@stack('css')
<style>
    .sw-main.sw-theme-default .step-anchor > li.active > a small {
        color: #B38E5D;
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

</style>
