<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-equal-height.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<style>
		.end-visit {
			border-radius: 3px;
			width: auto;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			width: auto;
			padding: 0px 12px;
			font-weight: 500;
			color: #ea1c2c;
			background: #ffffff;
		}
        .blink {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: -7px;
            right: -7px;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0% {opacity: 1;}
            50% {opacity: 0;}
        }
        .cart_toggle .dropdown-toggle::after {
            display: none;
        }
	</style>
</head>
<body>
    @php
        if (auth()->guard('web')->check()) {
            $userType = auth()->guard('web')->user()->type;
            $designation = auth()->guard('web')->user()->designation;

        }
    @endphp

    <div id="app">
        @if( !request()->is('login')  && !request()->is('register') )
        <nav class="topnavbar navbar navbar-expand-md">
            <div class="container-fluid px-0">
                <div class="logo">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <!-- {{ config('app.name', 'Laravel') }} -->
                        <img src="{{asset('admin/images/logo.png')}}" alt="" height="100" width="200">
                    </a>
                </div>
                <nav class="topNavigation">
                    <ul class="navbar-nav ml-auto align-items-center">
						<li class="toggle_menu">
                            <a class="nav-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
							
                           @php
                                $userId = auth()->guard('web')->user()->id;
                                $notifications = \App\Models\Notification::where('receiver_id', $userId)->orderBy('id', 'desc')->get();

                                $unreadNotifications = \App\Models\Notification::where('receiver_id', $userId)->where('read_flag', 0)->get();
                            @endphp
                            <a id="navbarDropdown" class="minicartBtn nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 0 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                @if($unreadNotifications->count() > 0) <span class="badge badge-danger">{{$unreadNotifications->count()}}</span> @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" style="    height: 300px;
    overflow-y: scroll;" aria-labelledby="navbarDropdown">
                                @forelse($notifications as $notification)
                                    <a class="dropdown-item" href="javascript: void(0)" onclick="readNotification('{{ $notification->id }}', '{{ $notification->route ? route($notification->route) : '' }}')" style="{{ ($notification->read_flag == 1) ? '' : 'background: #c7c7c7' }}">
                                        <h6 class="mb-1">{{$notification->title}}</h6>
                                        <p class="small text-muted mb-0 text-end">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans() }}</p>
                                    </a>
                                @empty
                                    <a href="javascript: void(0)" class="dropdown-item">
                                        No notifications yet
                                    </a>
                                @endforelse
                            </div>
                        </li>
                        @auth
                           
                        @endauth
                        @auth

                    @endauth
                        @guest
                            <li class="nav-item link__login">
                                <a class="nav-link" href="{{ route('login') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>{{ __('Login') }}</a>
                            </li>
                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @else
                            <li class="nav-item profileDrodown dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::guard('web')->user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <p class="dropdown-header">
                                        @php
                                            switch ($userType) {
                                                case 1: $userTypeDetail = "Vice President";break;
                                                case 2: $userTypeDetail = "Regional sales manager";break;
                                                case 3: $userTypeDetail = "Area sales manager";break;
                                                case 4: $userTypeDetail = "Area sales executive";break;
                                                case 5: $userTypeDetail = "Distributor";break;
                                                case 6: $userTypeDetail = "Retailer";break;
                                                default: $userTypeDetail = "";break;
                                            }
                                        @endphp
                                        {{$designation ? $designation : $userTypeDetail}}
                                    </p>
                                    <a class="dropdown-item" href="{{route('front.user.profile')}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        {{ __('Profile') }}
                                    </a>
									
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        {{ __('Logout') }}
                                    </a>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </nav>
            </div>
        </nav>

        <aside class="left_bar">
            <ul>
                <li class="nav-item {{ (request()->is('dashboard*')) ? 'active' : '' }}"><a href="{{ route('home') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Dashboard</a>
                </li>
				<li class="nav-item {{ (request()->is('salesperson*')) ? 'active' : '' }}"><a href="{{route('front.salesperson.list')}}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Sales Person</a>
                </li>
				<li class="nav-item {{ (request()->is('store/list*')) ? 'active' : '' }}"><a href="{{ route('front.store.list') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Store List</a>
                </li>
                @if($userType==2)
				<li class="nav-item {{ (request()->is('store/list*')) ? 'active' : '' }}"><a href="{{ route('front.store.list.approve') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                     Approve Retailers/Stores</a>
                 </li>
                 @endif
				<li class="nav-item {{ (request()->is('activity*')) ? 'active' : '' }}"><a href="{{ route('front.activity.index') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Activity Log</a>
                </li>
				<li class="nav-item {{ (request()->is('store/report*')) ? 'active' : '' }}"><a href="{{ route('front.store.order.report') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Store Wise Sales</a>
				</li>
				<li class="nav-item {{ (request()->is('activity*')) ? 'active' : '' }}"><a href="{{ route('front.product.order') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Product Wise Sales</a>
                </li>
				<li class="nav-item {{ (request()->is('activity*')) ? 'active' : '' }}"><a href="{{ route('front.zone.order') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    Zone/Area wise Sales</a>
                </li>
                {{-- not for Retailer --}}
               
               
                <li class="nav-item ">
                    <h5 class="acountText">Account</h5>
                </li>

                <li class="nav-item {{ (request()->is('user/profile*')) ? 'active' : '' }}">
                    <a href="{{route('front.user.profile')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profile</a>
                </li>
                
				

                <li class="nav-item">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout</a>
                </li>
            </ul>
        </aside>
        @endif

        <main class="mainbody {{ (request()->is('login*')) ? 'mainbodyNomargin' : '' }}">
            @yield('content')
        </main>
    </div>



    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
    <script src="{{ asset('js/jquery-equal-height.min.js') }}" defer></script>
   <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
		// display time
		var span = document.getElementById('clockShow');
		function timeDIsplay() {
		  var d = new Date();
		  var s = d.getSeconds();
		  var m = d.getMinutes();
		  var h = d.getHours();
		  span.textContent =
			("0" + h).substr(-2) + ":" + ("0" + m).substr(-2) + ":" + ("0" + s).substr(-2);
		}
		setInterval(timeDIsplay, 1000);

		// geolocation
		if (window.navigator.geolocation) {
			navigator.geolocation.watchPosition(showPosition);
			function showPosition(position) {
				$('input[name="start_lat"]').val(position.coords.latitude);
				$('input[name="start_lon"]').val(position.coords.longitude);
				$('input[name="end_lat"]').val(position.coords.latitude);
				$('input[name="end_lon"]').val(position.coords.longitude);
			}
		} else {
			console.log('Geolocation not supported by this browser');
		}

        // tooltip
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        // sweetalert fires | type = success, error, warning, info, question
        function toastFire(type = 'success', title, body = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 2000,
                timerProgressBar: false,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: type,
                title: title,
                // text: body
            })
        }

        // on session toast fires
        @if (Session::get('success'))
            toastFire('success', '{{ Session::get('success') }}');
        @elseif (Session::get('failure'))
            toastFire('warning', '{{ Session::get('failure') }}');
        @endif

        $('.storeCatgoryList a' ).on( 'click', function(e){
            var href = $(this).attr( 'href' );
            $('html, body').animate({
                scrollTop: $( href ).offset().top - 140
            });
            e.preventDefault();
            $(this).parent().addClass("current");
            $(this).parent().siblings().removeClass("current");
        });

        $("document").ready(function(){
            $('.jQueryEqualHeight').jQueryEqualHeight('.store_card');
        })

        function onlyNumberKey(evt) {
            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        $('.toggle_menu a').click(function(){
            $('.left_bar').toggleClass('active');
        });

        $('body').click(function(){
            $('.postcode-dropdown').removeClass('show');
        });

        $('input.dropdown-toggle').click(function(event){
            event.stopPropagation();
        });

        $('input.dropdown-toggle').click(function(){
            $('.postcode-dropdown').addClass('show');
        });

		// hide alert
		setTimeout(() => {
			$('.alert.alert-dismissible').hide();
		}, 5000);
		 // click to read notification
        function readNotification(id,route) {
            $.ajax({
                url: '{{ route("front.notification.read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(result) {
                    // console.log('{{ url()->current() }}',route);
                    // if (route != '' && '{{ url()->current() }}' != route) {
                    window.location = route;
                    // }
                }
            });
        }
    </script>

    @yield('script')
</body>
</html>
