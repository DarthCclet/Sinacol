@php
	$headerClass = (!empty($headerInverse)) ? 'navbar-inverse ' : 'navbar-default ';
	$headerMenu = (!empty($headerMenu)) ? $headerMenu : '';
	$headerMegaMenu = (!empty($headerMegaMenu)) ? $headerMegaMenu : '';
	$headerTopMenu = (!empty($headerTopMenu)) ? $headerTopMenu : '';
@endphp
<!-- begin #header -->
<div id="header" class="header {{ $headerClass }}">
	<!-- begin navbar-header -->
	<div class="navbar-header">
		@if ($sidebarTwo)
		<button type="button" class="navbar-toggle pull-left" data-click="right-sidebar-toggled">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		@endif

        <a href="/" class="navbar-brand"><span class=""><img src="{{asset('assets/img/logo/logo-stps-786x196.png')}}" alt=""></span></a>

        @if ($headerMegaMenu)
			<button type="button" class="navbar-toggle pt-0 pb-0 mr-0" data-toggle="collapse" data-target="#top-navbar">
				<span class="fa-stack fa-lg text-inverse">
					<i class="far fa-square fa-stack-2x"></i>
					<i class="fa fa-cog fa-stack-1x"></i>
				</span>
			</button>
		@endif

		@if (!$sidebarHide && $topMenu)
			<button type="button" class="navbar-toggle pt-0 pb-0 mr-0 collapsed" data-click="top-menu-toggled">
				<span class="fa-stack fa-lg text-inverse">
					<i class="far fa-square fa-stack-2x"></i>
					<i class="fa fa-cog fa-stack-1x"></i>
				</span>
			</button>
		@endif

		@if (!$sidebarHide && !$headerTopMenu)
		<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		@endif

		@if ($headerTopMenu)
			<button type="button" class="navbar-toggle" data-click="top-menu-toggled">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		@endif
	</div>
	<!-- end navbar-header -->

	@includeWhen($headerMegaMenu, 'includes.header-mega-menu')

    <div class="navbar-nav"></div>

	<!-- begin header-nav -->
	<ul class="navbar-nav navbar-right">
        <div class="navbar-brand f-s-24 text-secondary">Conciliación &nbsp;<b>STPS</b></div>
		<li class="dropdown">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle f-s-14">
				<i class="fa fa-bell"></i>
				<span class="label">5</span>
			</a>
			<div class="dropdown-menu media-list dropdown-menu-right">
				<div class="dropdown-header">NOTIFICATIONS (5)</div>
				<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
						<i class="fa fa-bug media-object bg-silver-darker"></i>
					</div>
					<div class="media-body">
						<h6 class="media-heading">Server Error Reports <i class="fa fa-exclamation-circle text-danger"></i></h6>
						<div class="text-muted f-s-10">3 minutes ago</div>
					</div>
				</a>
				<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
						<img src="/assets/img/user/user-1.jpg" class="media-object" alt="" />
						<i class="fab fa-facebook-messenger text-blue media-object-icon"></i>
					</div>
					<div class="media-body">
						<h6 class="media-heading">John Smith</h6>
						<p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
						<div class="text-muted f-s-10">25 minutes ago</div>
					</div>
				</a>
				<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
						<img src="/assets/img/user/user-2.jpg" class="media-object" alt="" />
						<i class="fab fa-facebook-messenger text-blue media-object-icon"></i>
					</div>
					<div class="media-body">
						<h6 class="media-heading">Olivia</h6>
						<p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
						<div class="text-muted f-s-10">35 minutes ago</div>
					</div>
				</a>
				<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
						<i class="fa fa-plus media-object bg-silver-darker"></i>
					</div>
					<div class="media-body">
						<h6 class="media-heading"> New User Registered</h6>
						<div class="text-muted f-s-10">1 hour ago</div>
					</div>
				</a>
				<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
						<i class="fa fa-envelope media-object bg-silver-darker"></i>
						<i class="fab fa-google text-warning media-object-icon f-s-14"></i>
					</div>
					<div class="media-body">
						<h6 class="media-heading"> New Email From John</h6>
						<div class="text-muted f-s-10">2 hour ago</div>
					</div>
				</a>
				<div class="dropdown-footer text-center">
					<a href="javascript:;">View more</a>
				</div>
			</div>
		</li>
		<li class="dropdown navbar-user">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-user"></i>
				<span class="d-none d-md-inline">{{isset($user->full_name)?:'Nombre Default'}}</span> <b class="caret"></b>
			</a>
			<div class="dropdown-menu dropdown-menu-right">
				<a href="javascript:;" class="dropdown-item">Edit Profile</a>
				<a href="javascript:;" class="dropdown-item"><span class="badge badge-danger pull-right">2</span> Inbox</a>
				<a href="javascript:;" class="dropdown-item">Calendar</a>
				<a href="javascript:;" class="dropdown-item">Setting</a>
				<div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item"
                   onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>


            </div>
		</li>
		@if($sidebarTwo)
		<li class="divider d-none d-md-block"></li>
		<li class="d-none d-md-block">
			<a href="javascript:;" data-click="right-sidebar-toggled" class="f-s-14">
				<i class="fa fa-th"></i>
			</a>
		</li>
		@endif
	</ul>
	<!-- end header navigation right -->
</div>
<!-- end #header -->
