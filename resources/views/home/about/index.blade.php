@extends('layouts.home')

@section('content')
    <section id="about-page">
        <div class="wrapper">
            <div class="box">
                <h2 class="title">
                    About <span class="highlight">Lurn</span>
                </h2>

                <hr class="mt-30">
                <h2 class="smaller title">
                    <span class="highlight">Lurn</span>
                    is an Online (& Offline) Transformational Home for Entrepreneurs Everywhere...
                </h2>

                <p>Founded by Anik Singal in 2004, our approach has always been a straightforward one:</p>
                <ol>
                    <li>To empower others to create & grow passion-based businesses...</li>
                    <li>To encourage big ideas that will change the world...</li>
                    <li>To educate people about how to be the best entrepreneur they can be - no matter where they are
                        on their journey...
                    </li>
                </ol>

                <hr class="mt-30">
                <h2 class="smaller title">
                    Our
                    <span class="highlight">Students</span>
                </h2>

                <p>Our students’ results are our most important measure of success.</p>
                <p>They drive every decision we make and everything we do, every single day. Because the truth is,
                    entrepreneurship can be a very lonely path and profession, but it doesn’t have to be. And we don't
                    want it to be - for anyone.</p>
                <p>Which is why we’re paving the way for a new kind of thinking.</p>
                <p>One that provides…</p>

                <ul>
                    <li><u><strong>Community</strong></u> that enables people to come together...</li>
                    <li><u><strong>Coaches</strong></u> that help to make dreams come true, in any way we can...</li>
                    <li><u><strong>Courses</strong></u> that teach the best systems and technologies to shatter
                        obstacles...
                    </li>
                </ul>

                <hr class="mt-30">
                <h2 class="smaller title">
                    How Can <span class="highlight">Lurn</span> Help?
                </h2>

                <p>As a member of Lurn Nation (get your free account here), you’ll get access to NEW TRAINING every
                    month on the most important topics for digital publishers, online marketers, and entrepreneurs.</p>
                <p><em>That way, you can always stay on top of the latest trends in digital publishing.</em></p>
                <p><strong>We have courses and training for EVERYTHING:</strong></p>

                <ul>
                    <li>SEO...</li>
                    <li>Copywriting...</li>
                    <li>Traffic...</li>
                    <li>Influencer Marketing...</li>
                    <li>Blogging...</li>
                    <li>Email Marketing...</li>
                    <li>Funnel-building...</li>
                    <li>Facebook Advertising...</li>
                    <li>Affiliate Marketing...</li>
                </ul>

                <p><strong><em>...and so much more!</em></strong></p>
                <p>You also get access to LURN's highly trained team of coaches which includes some of the most
                    successful digital publishers, marketers, and entrepreneurs from around the world.</p>

                {{-- <hr class="mt-30">
                <h2 class="smaller title">
                    What Do
                    <span class="highlight">I Do</span>
                    Now?
                </h2>

                <p>So...</p>
                <p>If you’ve been looking for the blueprint, community, and coaching that’ll help you finally be your
                    own person - welcome home. To get to the place where you can do whatever you want to do whenever you
                    want to do it, you just need someone to show you how.</p>
                <p>And that’s EXACTLY what Lurn is all about. Get your <strong>100% free Lurn Nation account below to
                        get started...</strong></p>

                <div class="align center mt-30">
                    <a href="{{ url('register') }}" class="ui huge yellow button" style="width: auto">
                        Get My Free Lurn Nation Account
                    </a>
                </div>
            </div>
        </div>

        <hr> --}}

       {{-- <div class="wrapper">
            <div class="box">
                <h2 class="title">
                    Meet Our <span class="highlight">Leadership Team</span>
                </h2>

                <div id="team">
                    <table class="person" data-video-id="208203972">
                        <tbody>
                        <tr>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/anik.jpg" alt="Anik Singal">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                            <td class="content">
                                <div class="name">Anik Singal, CEO</div>
                                <div class="description">
                                    <p>Sed tempor eu metus ut sollicitudin. Mauris lacus lacus, elementum eget tempus
                                        ac, molestie
                                        a ligula. Nunc non arcu vel mi gravida pulvinar facilisis id lacus. Vestibulum
                                        aliquet quis
                                        lorem vitae tempor. Mauris quam ante, vulputate eu vestibulum ac, aliquet
                                        porttitor elit.
                                        Integer tincidunt odio a aliquet ultricies. In quis auctor leo. Fusce in
                                        elementum nunc, sit
                                        amet dignissim massa. Curabitur odio nibh, dictum sed arcu tincidunt, pretium
                                        posuere
                                        tellus. Aenean sit amet ex blandit, aliquet enim vitae, sollicitudin lorem.
                                        Quisque eu
                                        sollicitudin mi.</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212337400">
                        <tbody>
                        <tr>
                            <td class="content">
                                <div class="name">Richard Ruggiero, CFO</div>
                                <div class="description">
                                    <p>Rich Ruggiero has been a successful Finance and Accounting professional, CFO, and
                                        Consultant
                                        for over 30 years. His focus has been with high growth and emerging companies
                                        such as
                                        Newbridge Networks ($1 Billion in annual sales sold to Alcatel) and Powerprecise
                                        Solutions
                                        (part of management team who executed the sale to Texas Instruments) as well as
                                        working for
                                        General Electric in their Financial Planning and Auditing Group.</p>
                                </div>
                            </td>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/rich.jpg" alt="Richard Ruggiero">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212340585">
                        <tbody>
                        <tr>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/olga.png" alt="Olga Geistfeld">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                            <td class="content">
                                <div class="name">Olga Geistfeld, Assistant VP of Operations</div>
                                <div class="description">
                                    <p>Olga joined the Lurn Team as the Customer Support Manager in 2014 and was quickly promoted to the Director of Operations. The fast paced environment and growth of the company are well aligned with her skills to adapt to change and continuously make sure that goals and deadlines are met. Olga holds a German Diploma in European Business Management.</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212336605">
                        <tbody>
                        <tr>
                            <td class="content">
                                <div class="name">Andrew Ellestad, Director of Technology</div>
                                <div class="description">
                                    <p>Andrew is a highly driven leader and technologist that blends business and
                                        strategy to
                                        deliver exceptional operational and financial results. Over 7 years of
                                        consulting,
                                        operational and business development experience in e-commerce and startup
                                        companies with a
                                        proven ability of growing and managing high-performing teams and complex
                                        projects to achieve
                                        business objectives. Andrew currently holds a BS in Business Administration and
                                        an MBA.</p>
                                </div>
                            </td>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/andrewe.jpg" alt="Andrew Ellestad">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212189626">
                        <tbody>
                        <tr>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/andrewl.jpg" alt="Andrew Lantz">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                            <td class="content">
                                <div class="name">Andrew Lantz, Director of Content</div>
                                <div class="description">
                                    <p>Andrew grew up in Farmington Hills, Michigan and attended the University of
                                        Michigan. While
                                        in college, he started his first online business - a writing agency that
                                        provided
                                        ghostwriting and copywriting services to clients all over the world. Anik became
                                        a client of
                                        his, and in 2015 Andrew joined the Lurn team on full time basis as the content
                                        director.</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212207604">
                        <tbody>
                        <tr>
                            <td class="content">
                                <div class="name">Dan Leman, Director of Marketing</div>
                                <div class="description">
                                    <p>Internet Marketing is one of Dan’s greatest passions and hobbies. He serves as
                                        Lurn’s
                                        Marketing Director and is largely responsible for Lurn's marketing strategies,
                                        initiatives,
                                        campaigns and partnership opportunities. Dan is an avid student of all things
                                        marketing and
                                        strives to stay on the cutting-edge, so he can continue to effectively lead the
                                        marketing
                                        team and share his knowledge.</p>
                                    <p>Dan lives in Australia with his wife and 2 children. He enjoys spending time with
                                        his
                                        family, coaching and playing soccer.</p>
                                </div>
                            </td>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/dan.jpg" alt="Dan Leman">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212209228">
                        <tbody>
                        <tr>
                            <td class="image">
                                <div class="photo">
                                    <img src="images/team/dave.jpg" alt="Dave Lovelace">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                            <td class="content">
                                <div class="name">Dave Lovelace, Director of Education</div>
                                <div class="description">
                                    <p>Dave is a native of North Carolina and joined the LURN team in 2011 as one of our
                                        top coaches and content creators. Today, he is our Director of Education.
                                        Professionally, Dave started his own 6-figure digital publishing business in
                                        2006 and has expert knowledge in product creation, audio, video, affiliate
                                        program management, product launches, sales funnels, copywriting, email
                                        marketing, affiliate marketing, WordPress, and more.</p>

                                    <p>Dave's first career, for 20 years, was in Radio (as a radio DJ) starting at age
                                        14. He once provided call-center support for Bloomingdales, Levis,
                                        1-800-Flowers, and Fingerhut. Before working full time online, he was an Account
                                        Manager for a major lifetime coatings (PVD) business that applied decorative
                                        finishes for OEM Kitchen and Bath companies such as American Standard, Symmons,
                                        and more as seen in stores like Lowes, Home Depot, etc.</p>

                                    <p>Some fun facts...</p>

                                    <ul>
                                        <li>From the age of 5, Dave played classical piano. Later in high school and
                                            college, he picked up Jazz piano and took instruction from the brother of
                                            the infamous Woody Herman. In his late 20's, he played Jazz standards along
                                            with his own compositions in a restaurant for a couple of Summers.
                                        </li>
                                        <li>In his early 20's, he went to modeling school which led to a paid trip to GQ
                                            Magazine in New York.
                                        </li>
                                        <li>Dave once created a country line dance called the "Side Saddle Slide".</li>
                                        <li>In his 30's, while operating a mobile DJ business, he created an electronic
                                            dance song under the name Groove U!
                                        </li>
                                        <li>He often is told "you look like Anderson Cooper!"</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="person" data-video-id="212200905">
                        <tbody>
                        <tr>
                            <td class="content">
                                <div class="name">Dianne Spivack</div>
                                <div class="description">
                                    <p>Maecenas non facilisis nisi. Donec fringilla lorem vel dolor rutrum sagittis. Nam
                                        sit amet sagittis eros. Interdum et malesuada fames ac ante ipsum primis in
                                        faucibus. Cras efficitur tellus a odio pharetra, id vehicula leo tincidunt.
                                        Quisque accumsan massa nec volutpat finibus. Ut eget vestibulum eros. Sed enim
                                        magna, sodales sit amet tempus sit amet, faucibus vel enim. Curabitur in eros
                                        orci. Phasellus malesuada turpis a turpis tincidunt malesuada. Donec egestas dui
                                        id diam egestas bibendum. Etiam venenatis ut libero at bibendum.</p>
                                </div>
                            </td>
                            <td class="image">
                                <div class="photo">
                                    <img src="http://unsplash.it/400/400" alt="Dianne Spivack">
                                    <div class="overlay"><i class="play icon"></i></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div> --}}
            </div>
        </div>
    </section>

    <div id="person-modal" class="ui basic modal">
        <i class="close icon"></i>
        <div class="content" style="text-align: center">
            <div style="display: inline-block; width: 100%; max-width: 800px">
                <div class="r16by9">
                    <iframe src frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/transition.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dimmer.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/modal.min.js"></script>
    <script>
        $(function () {
            var modal = $('#person-modal');

            modal.modal({
                onHide: function () {
                    modal.find('.r16by9').html('');
                },
            });

            $('.person .photo').on('click', function () {
                var videoId = $(this).parents('.person').data('video-id');

                modal.find('.r16by9').html(
                    '<iframe id="foo" width="100%" height="90%" allowfullscreen webkitallowfullscreen mozallowfullscreen src="http://player.vimeo.com/video/' + videoId + '?autoplay=1"></iframe>'
                );
                modal.modal('show');
            });
        });
    </script>
@endsection
