<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title>Lurn - A Transformational Home for Entrepreneurs!</title>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/vue/2.2.4/vue.min.js"></script>
	<link rel="stylesheet" type="text/css" href="{{ mix('css/home.css') }}">
</head>
<body>
	
	{{-- Top navigation --}}
	<div id="top-bar">
		<a id="logo" href="/">
			<img src="{{ url('images/logo-blue.png') }}" alt="Lurn Logo">
		</a>

		<div class="nav-toggle">
			<i class="fa fa-bars"></i>
		</div>

		<ul class="nav social" style="float: right">
			{{-- <li><a href="{{ route('login') }}" class="student-login-button">Student Login </a></li> --}}
			<li><a href="https://www.facebook.com/lurninc/"><i class="fa fa-facebook"></i></a></li>
			<li><a href="https://twitter.com/lurninc"><i class="fa fa-twitter"></i></a></li>
			<li><a href="https://www.youtube.com/user/aniksingalcom"><i class="fa fa-youtube"></i></a></li>
		</ul>

		<ul class="nav main">
			<li class="active"><a href="{{ route('home') }}">Home</a></li>
			<li><a href="{{ route('about') }}">About</a></li>
{{--		<li><a href="">Products</a></li>
			<li><a href="">Lurn Center</a></li> --}}
			<li><a href="/blog">Blog</a></li>
			<li><a href="{{ route('outreach') }}">Outreach</a></li>
			<li><a href="{{ route('contact') }}">Contact</a></li>
			<li class="hide-mobile">
                <form action="/blog/search/node" method="GET">
                    <div class="ui icon input">
                        <input class="prompt" type="text" placeholder="Search blog posts..." name='keys' />
                        <i class="search icon"></i>
                    </div>
                </form>
			</li>
			{{-- <li><a href="{{ route('login') }}">Student Login</a></li> --}}
		</ul>
	</div>

	{{-- Page header/banner --}}
	@hasSection('header')
		<header id="header" class="{{ Route::currentRouteName() }}">
			@yield('header')
		</header>
	@endif

	{{-- Page content --}}
	@yield('content')

	{{-- Footer area --}}
	<footer id="footer">
		<div class="top">
			<div class="ui grid stackable">
				<div class="four wide column about">
                    <img src="{{ url('images/logo-blue.png') }}" alt="Lurn Logo">
					<p>Lurn.com is the best source for all things digital publishing. On this site, you’ll discover the best advice from some of the most successful digital publishers in the world.</p>

					<a href="https://www.facebook.com/lurninc/"><i class="fa fa-facebook"></i></a>
					<a href="https://twitter.com/lurninc"><i class="fa fa-twitter"></i></a>
					<a href="https://www.youtube.com/user/aniksingalcom"><i class="fa fa-youtube"></i></a>
					<a href="https://www.instagram.com/lurninc/"><i class="fa fa-instagram"></i></a>
				</div>
				<div class="four wide column">
					<h5>Get Involved</h5>
					<ul>
						<li><a href="{{ route('career') }}">Careers</a></li>
						<li><a href="{{ route('outreach') }}">Outreach</a></li>
					</ul>
				</div>
				<div class="four wide column">
					<h5>Keep Lurning</h5>
					<ul>
						<li><a href="{{ route('about') }}">About Us</a></li>
						{{--<li><a href="#">Products</a></li>
						<li><a href="/">Learn Center</a></li>--}}
						<li><a href="/blog/">Blog</a></li>
					</ul>
				</div>
				<div class="four wide column">
					<h5>Get in Touch</h5>
					<ul class="with-icons">
						<li><i class="fa fa-map-marker"></i>2098 Gaither Road<br>Rockville, MD 20850</li>
						<li><i class="fa fa-phone"></i>(888) 477 9719<br>Extension 2</li>
						<li><i class="fa fa-envelope"></i>support&#64;lurn.com</li>
						<li><i class="fa fa-globe"></i>www.lurn.com</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="aligned center padded">
			<div class="bottom">
				Lurn.com is a trademark of Lurnc Inc. &copy; {{ date('Y') }}
				| <a href="{{ route('privacy') }}">Privacy Policy</a>
				| <a href="{{ route('terms') }}">Terms of Use</a>
				| <a href="{{ route('dmca') }}">DMCA Notice</a>
				| <a href="{{ route('refund') }}">Refund Policy</a>
				| <a href="{{ route('anti-spam') }}">Anti-Spam</a>
				| <a href="{{ route('sms-privacy') }}">SMS Policy</a>
			</div>
		</div>
	</footer>

	<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.11/components/transition.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.11/components/dropdown.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.11/components/dimmer.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.11/components/modal.min.js"></script>
	<script src="{{ mix('/js/home.js') }}"></script>
	<script src="{{ mix('js/global.js') }}"></script>
	<script>
		$(function () {
			$('.nav-toggle').on('click', function () {
				$(this).toggleClass('active');
				$('.nav.main').toggleClass('open');
            });
        });
	</script>
	@yield('scripts')
</body>
</html>
