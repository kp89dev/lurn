@extends('layouts.home')

@section('content')
    <section id="career-page">
        <div class="box align center career-video">
            <iframe allowfullscreen="" allow="autoplay; encrypted-media" src="https://www.youtube.com/embed/eCIy37F952o?rel=0&showinfo=0&autoplay=0" width="100%" height="100%" frameborder="0"></iframe>
        </div>
        <div class="wrapper">
            <div class="box">
                <h2 class="smaller title">
                    About <span class="highlight">Lurn</span>
                </h2>
                <h3 class="spacer top">At Lurn, J-O-B is a 4-Letter Word...Play!</h3>
                <p>We believe that work is play and play is work.</p>
                <p>Work has to be fun, educational, and make you leap out of bed so excited, you can’t wait to get started.</p>
                <p>Lurn is a high growth company delivering online education and content for the web and mobile devices. We have been in business for over a decade, and continue on a high growth path. We foster an entrepreneurial and team spirit around a set of key core values of problem resolution, team building, and balancing work and life.</p>
                <p>Lurn offers a very competitive compensation package including bonus incentives and complete benefit package.</p>
                <p>Welcome to Lurn, Inc.</p>
            </div>
        </div>
        <div class="box align center secondary">
            <h2 class="title">
                7 Reasons to Build Your Career at <span class="highlight walsheim-medium">Lurn</span>
            </h2>
            <div class="wrapper align left">
                <div class="ui column grid stackable">
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#1. Freedom to Be Yourself</h3>
                        <p>We understand that brilliant people hate rules and shackles, and desire the freedom to do their work — in their way. This is not a 9-5 job. You can choose your own hours. As long as you’re productive, you’re free to do it your way.</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#2. Amazing Teammates</h3>
                        <p>Our work culture attracts brilliant applicants from around the world. Make it in, and you’ll be working with some of the best minds in the business. We understand that “A” people attract “A” people. We recruit people who are not just brilliant, but people who are talented, driven, positive and live life with a purpose. Get in, and you’ll be surrounded with friends, peers and a network that will help you move your life to a whole new level.</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#3. Fun Like the Adults Aren’t Watching</h3>
                        <p>Work is play and play is work. Don’t get us wrong, we’re a disciplined, well-oiled engine of growth. But we believe that business should be fun and people should look forward to going to work each day. We create a work environment that is designed to make your tasks, your teammates and even company meetings, a ton of fun.</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#4. Entrepreneurship</h3>
                        <p>We understand that great people dream of starting their own companies. We help you attain this dream. As creators of the top training programs in internet entrepreneurship, you are constantly exposed to the attitude and the education necessary for starting your own business. Simply put, you are free to learn from us. Most of our team members become products of our products and create their own online businessess.</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#5. Competitive Salary and HUGE Bonuses</h3>
                        <p>We offer highly competitive salaries and bonuses depending on performance. The more you contribute, the more you make. No joke - some team members make double their salaries in bonuses!</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#6. Unlimited Vacations</h3>
                        <p>We DO expect you to take vacations. Burnt out is NOT the new Black.</p>
                    </div>
                    <div class="column sixteen wide blog-column ui card"> 
                        <h3>#7. Awesome Benefits</h3>
                        <p>We’re not just talking Health, Dental, and a 401k with a big matching plan. We’re talking brand new MacBook Pro or PC. We’re talking reimbursed high speed internet.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper">
            <div class="box">
                <h2 class="title">
                    Available Positions
                </h2>
                <div class="ui two column grid stackable">
                    <div class="column"> 
                        <a class="button primary block single-position" href="{{ route('webdeveloper') }}">Software Developer</a>
                        <a class="button primary block single-position" href="{{ route('juniorcopywriter') }}">Junior Copywriter</a>
                        <a class="button primary block single-position" href="{{ route('lurncentermanager') }}">Lurn Center Manager</a>
                    </div>
                    <div class="column"> 
                        <a class="button primary block single-position" href="{{ route('customerhappinessspecialist') }}">Customer Happiness Specialist</a>
                        <a class="button primary block single-position" href="{{ route('associatecontentmanager') }}">Associate Content Manager</a>
                        <a class="button primary block single-position" href="{{ route('designer') }}">Designer</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
