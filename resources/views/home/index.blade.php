@extends('layouts.home')

@section('header')
	<h1>Join the New Online Home <span class="highlight">For Entrepreneurs</span></h1>

	<div class="wrapper">
		<p class="copy">
			Discover the best practices for starting, launching and growing successful online businesses!
            We're launching <strong>a brand new online entrepreneurial training hub</strong> just for you!
			Get on the Early Bird Email List below & we'll let you know when we hit the launch button (Coming April 2018)
		</p>
	</div>

	{{-- <form onsubmit="return goToRegister(this)"> --}}
    <form name="subscriptionFrm_21494" id="subscriptionFrm_21494" action="https://lurnnation.sendlane.com/form/21494" method="post">
        <input type="hidden" name="form_id" id="form_id" value="21494">
		<div class="create-account-wrapper">
            {{-- <input type="email" name="email" value="" placeholder="Email Address" required> --}}
            <input id="form_field[3]" name="form_field[3]" type="text" placeholder="Email Address" required>
			<button class="create-account">GET ON THE LIST</button>
		</div>
	</form>
@endsection

@section('content')
	{{-- Featured on section --}}
	<section class="featured">
		<div class="wrapper">
			<ul>
				<li><img src={{$cdn_url."images/features/ted.svg"}} width="64"></li>
				<li><img src={{$cdn_url."images/features/inc500.svg"}} height="48"></li>
				<li><img src={{$cdn_url."images/features/theHuffingtonPost.svg"}} height="64"></li>
				<li><img src={{$cdn_url."images/features/cbs.svg"}} width="72"></li>
				<li><img src={{$cdn_url."images/features/businessWeek.svg"}} width="128"></li>
				<li><img src={{$cdn_url."images/features/amazonBestSeller.svg"}} width="64"></li>
				<li><img src={{$cdn_url."images/features/abc.svg"}} height="48"></li>
				<li><img src={{$cdn_url."images/features/entrepreneur.svg"}} width="128"></li>
			</ul>
		</div>
	</section>
	{{-- LURN EDUCATES section --}}
	<div class="box align center">
        <div class="heading-icon"><img src={{$cdn_url."images/home/icons/icon-head-educate.png"}}></div>

		<h2 class="title walsheim-bold">
			<span class="highlight">Lurn</span> Educates
		</h2>

		<p class="wrapper tx larger">
			<span class="bold highlight">Lurn</span> is a community of entrepreneurs exchanging the best information
			on what is actually working in the digital marketing world today.
		</p>

		<div class="ui grid stackable" style="margin-top: 1em">
			<div class="three wide column centered">
				<img src={{$cdn_url."images/home/icons/icon-clock.png"}}>
				<p class="tx largest dark block walsheim-medium counter" style="margin-bottom:0;">15</p>
				<p class="tx accented block walsheim-medium">Years</p>
			</div>
			<div class="three wide column centered">
				<img src={{$cdn_url."images/home/icons/icon-person.png"}}>
				<p class="tx largest dark block walsheim-medium counter" style="margin-bottom:0;">10,057</p>
				<p class="tx accented block walsheim-medium">Members</p>
			</div>
			<div class="three wide column centered">
				<img src={{$cdn_url."images/home/icons/icon-envelope.png"}}>
				<p class="tx largest dark block walsheim-medium counter" style="margin-bottom:0;">649,651</p>
				<p class="tx accented block walsheim-medium">Subscribers</p>
			</div>
		</div>
	</div>
	{{-- Recent activities section
	<div class="box align center secondary">
		<p class="tx larger dark">Recent Activities in Community:</p>

		<div class="wide-wrapper spacer top">
			<div class="ui three column grid stackable">
				@foreach ($activities as $activity)
					<div class="column">
						<div class="activity-card">
                            <div class="activity-image">
								<img src="http://cdn.lurn.com/{{$activity->user->setting->image}}"/>
							</div>
							<div class="activity-info">
								<span class="user">{{ $activity->user->name }}</span>
								<span class="activity">{{ $activity->activity_text }}</span>
								<span class="time">{{ $activity->activity_time->diffForHumans() }}</span>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div> --}}
	{{-- Courses section 
	<div class="box align center">
		<h2 class="title walsheim-medium">
			Get <span class="highlight walsheim-medium">Lurn</span> Certified
		</h2>

		<p class="wide-wrapper tx large">
			In the digital jungle of online entrepreneurship, stand out by being Lurn Certified. Thousands of
			students across multiple continents are getting more clients, closing more deals, and getting better
			jobs by adding the Lurn Certification badge on their profile.
		</p>

		<div class="get-certified wide-wrapper centered align left">
			<div class="ui four column grid stackable spacer top">
				@foreach ($featuredCourses as $fcourse)
					<div class="column course-column">
						<div class="ui card" style="margin: auto">
							<div class="image">
								<img src="{{ $fcourse->course->getPrintableImageUrl() }}">
							</div>
							<div class="content">
								<a class="title" href="{{ route('course', $fcourse->course->slug) }}">{{ $fcourse->course->title }}</a>
								<div class="description tx accented spacer top">
									<span class="course-snippet tx light">{!! $fcourse->course->snippet !!}</span>
								</div>
							</div>
                            <div class="extra content">
                                <table>
                                    <tr>
                                        <td>
                                            <i class="fa fa-user icon-circle-small"></i>
                                            {{ number_format($fcourse->course->getCounters()->students) }}
                                        </td>
                                        <td>
                                            <i class="fa fa-thumbs-up"></i>
                                            {{ number_format($fcourse->course->getCounters()->likes)  }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
						</div>
					</div>
				@endforeach
			</div>
		</div>

		<p class="spacer top">Looking for other products? <a href="#">Discover more Lurn Products &nbsp; <i class="icon-circle fa fa-angle-right"></i></a></p>
	</div> --}}
	{{-- Lurn Nation section --}}
	<div class="box secondary" style="padding: 0">
		<div class="background-fade-container">
			<div class="background-right" style="background-image: url(/images/bkg-lurn-nation.jpg)"></div>
			<div class="fade-box-left">
				<div class="fade-box-content">
					<h3>
						<span class="accent">Coming Soon:</span> <span class="highlight walsheim-medium">Lurn</span> Nation
					</h3>
					<p>
						As a member of Lurn Nation, you get access to NEW TRAINING every month on the
						most important topics for digital entrepreneurs. Stay on top of latest trends,
						lurn the latest strategies, and use the best tools in the business. You also
						get access to Lurn's highly trained team of coaches, which includes some of
						the most successful digital publishers from around the world.
					</p>

                    <form class="ui two column grid" name="subscriptionFrm_21494" id="subscriptionFrm_21494" action="https://lurnnation.sendlane.com/form/21494" method="post">
                        <input type="hidden" name="form_id" id="form_id" value="21494">
						<div class="column center">
							<div class="ui fluid input">
								<input class="prompt stand-alone" type="email" placeholder="Enter your best email address" id="form_field[3]" name="form_field[3]" required/>
							</div>
						</div>
                        <div class="column">
                            <button class="button primary block">Join the Waiting List</button>
                        </div>
						{{-- <div class="column">
							<p class="tx accented light"><em>Officially releasing January 2018</em></p>
						</div> --}}
					</form>
				</div>
			</div>
		</div>
	</div>
	{{-- LURN EMPOWERS section --}}
	<div class="box align center">
		<div class="heading-icon"><img src={{$cdn_url."images/home/icons/icon-head-empower.png"}}></div>

		<h2 class="title walsheim-bold">
			<span class="highlight">Lurn</span> Empowers
		</h2>

		<p class="wrapper tx larger">
			With our FREE entrepreneurship training courses, we're able to leave a global impact!
			But don't just take our word for it...
		</p>

		<div class="wide-wrapper spacer top">
			<div class="ui three column grid stackable">

				<div class="column">
					<div class="testimonial">
						<div class="quote">
							<div class="quote-tail"></div>
							<div class="description">
								"We need more people who are honest, legitimate, moral,
								ethical, and do business to make the world a better place..."
							</div>
						</div>
						<div class="testimonial-card">
							<div class="testimonial-image">
								<img src={{$cdn_url."images/home/testimonials/profile-robert-kiyosaki.png"}}>
							</div>
							<div class="meta">
								<span class="author">Robert Kiyosaki</span>
								<span class="about">Author of Best-Selling "Rich Dad Poor Dad"</span>
							</div>
						</div>
					</div>
				</div>

				<div class="column">
					<div class="testimonial">
						<div class="quote">
							<div class="quote-tail"></div>
							<div class="description">
								"If I want to learn something, I go to someone who has
								demonstrated by results that they know. Anik Singal knows!"
							</div>
						</div>
						<div class="testimonial-card">
							<div class="testimonial-image">
								<img src={{$cdn_url."images/home/testimonials/profile-bob-proctor.png"}}>
							</div>
							<div class="meta">
								<span class="author">Bob Proctor</span>
								<span class="about">Thought Leader and Teacher in "The Secret"</span>
							</div>
						</div>
					</div>
				</div>

				<div class="column">
					<div class="testimonial">
						<div class="quote">
							<div class="quote-tail"></div>
							<div class="description">
								"Anik's teachings have not only had an impact on my life,
								but have transformed my kids' lives. Powerful stuff!"
							</div>
						</div>
						<div class="testimonial-card">
							<div class="testimonial-image">
								<img src={{$cdn_url."images/home/testimonials/profile-les-brown.png"}}>
							</div>
							<div class="meta">
								<span class="author">Les Brown</span>
								<span class="about">Best Selling Author, Coach, and Speaker</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{{-- Bootcamps section 
	<div class="box align center secondary">
		<h2 class="title">
			<span class="highlight walsheim-medium">FREE</span> Bootcamps!
		</h2>

		<p class="wrapper tx large">
			We're hard at work in Lurn Headquarters dissecting business strategies and scaling
			them to build profitable businesses. Then we create a Step-by-Step Bootcamp Course
			leveraging those strategies &mdash; and release it for FREE!
		</p>

		<div class="wide-wrapper spacer top">
			<div class="ui three column grid stackable">
				@foreach ($featuredBootcamp as $bcourse)
					<div class="column bootcamp-column">
						<div class="bootcamp-card">
							<div class="image">
								<img src="{{ $bcourse->course->getPrintableImageUrl() }}">
							</div>
							<div class="content">
								<div class="header">
									<a href="{{ route('course', $bcourse->course->slug) }}">{{ $bcourse->course->title }}</a>
								</div>
								<div class="description">
									<div class="tx accented light course-snippet">
										{!! $bcourse->course->snippet !!}
									</div>

									<a href="{{ route('course', $bcourse->course->slug) }}" class="button accent block">Enroll in Bootcamp</a>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div> --}}
	{{-- Lurn Center section --}}
	<div class="box secondary" style="padding: 0">
		<div class="background-fade-container">
			<div class="background-left" style="background-image: url(/images/bkg-lurn-center.jpg)"></div>
			<div class="fade-box-right">
				<div class="fade-box-content">
					<h3>
						<span class="accent">Coming Soon:</span> Lurn Center
					</h3>
					<p>
						We're not just a virtual HOME for Entrepreneurs. Soon, we will open doors to a high-tech
						25,000 square foot facility for entrepreneurs who want the BEST environment possible
						to grow their business. With large auditoriums, multiple classrooms, and new-age work
						stations - simply walk in and take your business to the next level.
					</p>

                    <form name="subscriptionFrm_21495" id="subscriptionFrm_21495" class="ui two column grid" action="https://lurnnation.sendlane.com/form/21495" method="post">
                        <input type="hidden" name="form_id" id="form_id" value="21495">
                        <div class="column center">
                            <div class="ui fluid input">
                                <input class="prompt stand-alone" id="form_field[3]" name="form_field[3]" type="text" placeholder="Enter your best email address" required>
                            </div>
                        </div>
                        <div class="column">
                            <button class="button accent block">Join the Waiting List</button>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	{{-- Blog section --}}
	<div class="box align center secondary">
		<h2 class="title">
			<span class="highlight walsheim-medium">Lurn</span> Blog
		</h2>
		<p class="wrapper tx large">
			Cutting-edge business strategies, latest technologies, mindset hacks and a
			lot more - published on our blog several times a week. Take your business
			to the next level with these actionable posts. Check out what's new...
		</p>
        <div id="post-container" class="wide-wrapper spacer top">
            <div v-cloak v-show="posts.length" style="display: none;" class="ui column grid stackable">
                <div v-for="post in posts.slice(0,1)" class="column  eight wide blog-column">
                    <a :href="post.path" class="trackable blog-card">
                        <div class="image" style="width: 100%">
                            <img :src="post.field_image">
                        </div>
                        <div class="content">
                            <div class="header">
                                @{{ post.title }}
                            </div>
                            <div class="description">
                                <p class="tx accented light">
                                    @{{post.body}}
                                </p>
                            </div>
                            <div>
                                <span class="date"><i class="wait icon"></i> @{{post.created}}</span> <span style="float:right"><i class="unhide icon"></i> @{{post.view_count}}</</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div v-for="post in posts.slice(1,2)" class="column four wide blog-column">
                    <a :href="post.path" class="trackable blog-card">
                        <div class="image" style="width: 100%">
                            <img :src="post.field_image">
                        </div>
                        <div class="content">
                            <div class="header">
                                @{{ post.title }}
                            </div>
                            <div class="description">
                                <p class="tx accented light">
                                    @{{post.body}}
                                </p>
                            </div>
                            <div>
                                <span class="date"><i class="wait icon"></i> @{{post.created}}</span> <span style="float:right"><i class="unhide icon"></i> @{{post.view_count}}</</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div v-for="post in posts.slice(2,3)" class="column four wide blog-column">
                    <a :href="post.path" class="trackable blog-card">
                        <div class="image" style="width: 100%">
                            <img :src="post.field_image">
                        </div>
                        <div class="content">
                            <div class="header">
                                @{{ post.title }}
                            </div>
                            <div class="description">
                                <p class="tx accented light">
                                    @{{post.body}}
                                </p>
                            </div>
                            <div>
                                <span class="date"><i class="wait icon"></i> @{{post.created}}</span> <span style="float:right"><i class="unhide icon"></i> @{{post.view_count}}</</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <p class="spacer top">Read more Blog Posts. <a href="/blog">Visit Lurn Blog &nbsp; <i class="icon-circle fa fa-angle-right"></i></a></p>
	</div>
	{{-- LURN ENABLES section --}}
	<div class="box align center">
		<div class="heading-icon"><img src={{$cdn_url."images/home/icons/icon-lightbulb.png"}}></div>

		<h2 class="title walsheim-bold">
			<span class="highlight walsheim-bold">Lurn</span> Enables
		</h2>

		<p class="wrapper tx larger">
			Help Lurn make the world a better place with our outreach program that spans
			3 continents. Get involved today...
		</p>

		<p class="tx accented spacer top">
			In Partnership with:
		</p>

		<img src={{$cdn_url."images/logo-forallourgood.png"}} class="spacer top">

		<div class="wide-wrapper spacer top">
			<div class="ui two column grid stackable">
				<div class="column align left">
					<p class="tx largest highlight">Dream Centers</p>
					<p class="tx large">A self-sustainable, non-profit organization that envisions a world where every child has the opportunity to flourish and unleash their entrepreneurial spirit.</p>
					<p class="tx large">The path to prosperity for every child begins with clean water, sound nutrition, electricity and education.</p>
					<p class="tx large">For All Our Good seeks out and supports social entrepreneurs with novel ideas for delivering the essentials of prosperity to children around the globe.</p>

					<a href="http://forallourgood.org/" target='_blank' class="button primary block" style="width: 50%">Learn How You Can Help</a>
				</div>

				<div class="column">
					<img src={{$cdn_url."images/dream-centre-thumb.png"}} class="rounded">

					<div class="ui three column grid spacer top">
						<div class="column"><img src={{$cdn_url."images/dream-centre-thumb.png"}} class="rounded"></div>
						<div class="column"><img src={{$cdn_url."images/dream-center.png"}} class="rounded"></div>
						<div class="column"><img src={{$cdn_url."images/dreamcenterlogo.png"}} class="rounded" style="width: 100%; height: 97%"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{{-- Village enterprises section --}}
	<div class="box align center secondary">
		<div class="wide-wrapper">
			<div class="ui two column grid stackable">
				<div class="column align left">
					<img src={{$cdn_url."images/village-enterprise-logo1.png"}}>
				</div>

				<div class="column align left">
					<p class="tx largest highlight spacer top walsheim-medium">Village Enterprise</p>
					<p class="tx large">They go to a Village in East Africa and essentially pull an ENTIRE village out of extreme poverty... Through ENTREPRENEURSHIP. They adopt a village and help fund at least 50 “Micro-Businesses” in the village with just $500 each. That alone is enough to help raise the standard of living of 1,000+ people by over 40%!</p>

					<a href="{{route('outreach')}}" class="button primary block spacer top" style="width: 50%">Join Lurn Insider</a>
				</div>
			</div>
		</div>
	</div>
	{{-- TEDx video section --}}
	<div class="box align center tedx-video">
		<div class="cover">
			<p class="tx larger" style="color: #fff; padding-top: 220px">
				Watch Anik Singal's Empowerment TEDx Talk
			</p>
			<img src={{$cdn_url."images/play-button.png"}} class="spacer top" style="cursor: pointer">
		</div>
	</div>
	<form name="subscriptionFrm_21494" id="subscriptionFrm_21494" class="form-horizontal" action="https://lurnnation.sendlane.com/form/21494" method="post">
        <input type="hidden" name="form_id" id="form_id" value="21494">
		{{-- Call to action: register --}}
		<div class="call-to-action">
			<div class="ui grid stackable">
				<div class="eight wide column">
                    <h3 class="walsheim-bold">Get on the Early Bird List & We'll let You Know the Second "Lurn Nation" Goes LIVE</h3>
				</div>
				<div class="five wide column">
					<input id="form_field[3]" name="form_field[3]" type="text" placeholder="Enter your email address ..." required>
				</div>
				<div class="three wide column">
					<button class="button accent">GET ON THE LIST</button>
				</div>
			</div>
		</div>
	</form>

    @if (($email = request('email', request('remote-registration'))) !== null)
		<iframe src="{{ route('onboarding.signup') }}?email={{ $email }}" class="overlay-iframe" frameborder="0"></iframe>
    @endif
@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery.counterup@2.1.0/jquery.counterup.min.js"></script>
	<script>
        function equalizeHeights(selector) {
            var heights = new Array();

            $(selector).each(function() {

                $(this).css('min-height', '0');
                $(this).css('max-height', 'none');
                $(this).css('height', 'auto');
                heights.push($(this).outerHeight());
            });
            var max = Math.max.apply( Math, heights );
            $(selector).each(function() {
                $(this).css('height', max + 'px');
            });
        }

		$(document).ready(function() {
			$('.counter').counterUp({
				time: 1200
			});

			$('.tedx-video').on( "click", function() {
				var iframe = document.createElement("iframe");

				iframe.setAttribute("frameborder", "0");
				iframe.setAttribute("allowfullscreen", "");
				iframe.setAttribute("allow", "autoplay; encrypted-media");
				iframe.setAttribute("width", '100%');
				iframe.setAttribute("height", '100%');
				iframe.setAttribute("src", "https://www.youtube.com/embed/ti6S9EUO_UA?rel=0&showinfo=0&autoplay=1");

				this.innerHTML = "";
				this.appendChild( iframe );
			} );

            equalizeHeights($('.column .activity-card'));
            equalizeHeights($('.course-column .description'));
            equalizeHeights($('.course-column .title'));
            equalizeHeights($('.column .testimonial .quote'));
            equalizeHeights($('.column .bootcamp-card .header'));
            equalizeHeights($('.column .bootcamp-card .description .tx'));
            equalizeHeights($('.column.four .blog-card .header'));
            equalizeHeights($('.column.four .blog-card .description'));
            equalizeHeights($('.blog-column .blog-card'));
            var iv = null;
            $(window).resize(function() {
                if(iv !== null) {
                    window.clearTimeout(iv);
                }

                iv = setTimeout(function() {
                    equalizeHeights($('.course-column .description'));
                    equalizeHeights($('.course-column .title'));
                    equalizeHeights($('.column .activity-card'));
                    equalizeHeights($('.column .testimonial .quote'));
                    equalizeHeights($('.column .bootcamp-card .header'));
                    equalizeHeights($('.column .bootcamp-card .description .tx'));
                    equalizeHeights($('.blog-column .blog-card'));
                }, 120);
            });
		});
	</script>
@endsection
