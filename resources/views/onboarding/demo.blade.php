@extends('layouts.chute')

@section('content')
    <style>
        #demo-step table {
            max-width: 1155px;
            margin: 40px auto;
        }

        #demo-step table td {
            vertical-align: middle;
        }

        #demo-step .green-panel {
            background-color: #b8d4a4;
            padding: 30px 40px;
            font-size: 28px;
            line-height: normal;
            font-weight: normal;
            border-radius: 15px 0 0 15px;
            box-shadow: -3px 7px 10px 0 rgba(0, 0, 0, .1)
        }

        #demo-step .green-panel > div {
            position: relative;
            padding-left: 40px;
            margin: 20px 0;
        }

        #demo-step .green-panel > div i.check {
            position: absolute;
            top: 5px;
            left: 0;
        }

        #demo-step .benefits {
            max-width: 1000px !important;
        }

        #demo-step .benefit {
            background: #eff1f4 5px center no-repeat;
            background-size: 70px;
            min-height: 100px;
            height: 100%;
            font-weight: bold;
            vertical-align: middle;
            padding: 15px 10px 15px 80px;
            border-radius: 10px;
            font-size: 20px;
            line-height: 26px;
            display: flex;
            align-items: center;
        }

        #demo-step .benefit.b1 {
            background-image: url(/images/home/icons/demo-ob-page/icon-play.png);
        }

        #demo-step .benefit.b2 {
            background-image: url(/images/home/icons/demo-ob-page/icon-lock.png);
        }

        #demo-step .benefit.b3 {
            background-image: url(/images/home/icons/demo-ob-page/icon-star.png);
        }

        .center-aligned {
            text-align: center;
        }
        
        .main-button {
            background-color: #ebc71d;
            color: #233e6b;
            font-weight: 900;
            font-size: 40px;
            padding: 20px 40px 15px;
            font-family: gt_walsheim_probold, sans-serif;
            border-radius: 100px;
            box-shadow: 0 5px 10px 0 rgba(0, 0, 0, .2);
        }

        .main-button:hover {
            background-color: #ffd81f;
            color: #233e6b;
        }
    </style>

    <div id="demo-step">
        <div class="ui grid container">
            <div class="column sixteen wide" style="padding-right:2rem; padding-left:2rem;">
                <h1 style="font-size: 48px; text-align: center; margin-bottom: 0;">CONGRATS! Your Account is Ready to Go!</h1>
                <p style="font-size: 36px; text-align: center; margin-top: .5em;">
                    Now, Watch this Demo on How To <strong><u>Get a $500 course for FREE...</u></strong>
                </p>
            </div>
        </div>

        <table>
            <tr>
                <td width="40%">
                    <div class="green-panel">
                        <div>
                            <i class="check icon"></i>
                            Watch This <strong>QUICK VIDEO</strong> of How To Get The Most Out of Lurn Nation...
                        </div>
                        <div>
                            <i class="check icon"></i>
                            <u>Revealed:</u> How To Unlock <strong>HIDDEN BONUSES...</strong>
                        </div>
                        <div>
                            <i class="check icon"></i>
                            See How to Earn Points & <strong>Win AWESOME PRIZES!</strong>
                        </div>
                    </div>
                </td>
                <td>
                    <iframe src="https://www.youtube.com/embed/N6Y5It4nq2g" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width: 100%; height: 400px"></iframe>
                </td>
            </tr>
        </table>

        <div class="benefits ui three columns grid container">
            <div class="column">
                <div class="benefit b1">
                    All-In-One Business & Marketing Training...
                </div>
            </div>
            <div class="column">
                <div class="benefit b2">
                    Never Pay for Your Membership (Always Free)!
                </div>
            </div>
            <div class="column">
                <div class="benefit b3">
                    Courses for soloprenuers, advanced & beginner marketers...
                </div>
            </div>
        </div>

        <div class="container center-aligned" style="margin-top: 100px; padding-bottom: 100px;">
            <a href="{{ isset($course) ? $course->url : route('dashboard') }}?onboardme" class="main-button">
                YES - TAKE ME INSIDE!
            </a>
        </div>

        <style>
            #about-anik table {
                width: 100%;
            }

            #about-anik table td {
                vertical-align: top;
            }

            #about-anik .photo {
                line-height: 0;
                width: 200px;
            }

            #about-anik .photo img {
                width: 100%;
                border-radius: 100px;
            }

            #about-anik h3 {
                font-size: 32px;
                font-family: gt_walsheim_promedium, sans-serif;
                color: #1b3e6f;
                margin: 0;
            }

            #about-anik h4 {
                font-size: 22px;
                font-family: gt_walsheim_proregular, sans-serif;
                color: #1b3e6f;
                margin: 0 0 15px;
            }

            #about-anik p {
                font-size: 16px;
                color: #585b69;
            }

            #about-anik .description {
                padding: 0 30px;
            }

            #about-anik .connect {
                width: 300px;
                text-align: center;
            }

            #about-anik .connect ul {
                margin: 15px 0 0;
                padding: 0;
                font-size: 0;
                line-height: 0;
            }

            #about-anik .connect ul li {
                display: inline-block;
                font-size: 32px;
            }

            #about-anik .connect ul li a {
                display: block;
                padding: 10px;
                color: #afb4bb;
            }

            #about-anik .connect ul li a:hover {
                color: #1b3e6f;
            }

            #about-anik .connect ul li i.fa {
                padding: 0;
            }
        </style>

        <div id="about-anik">
            <table>
                <tr>
                    <td class="photo">
                        <img src="{{ cdnurl('images/team/anik.jpg') }}">
                    </td>
                    <td class="description">
                        <h3>About Anik Singal</h3>
                        <h4>Lurn, Inc. Founder &amp; CEO</h4>
                        <p>With over 15 years of experience in online marketing, teaching over 350,000 students around the world, and generating over $150 million for himself and his clients, Anik is widely considered one of today’s most successful digital marketing experts. His specialties include profit generating product launches, funnel building, article marketing, search engine optimization, affiliate marketing & business management consulting.</p>
                    </td>
                    <td class="connect">
                        <h3>Connect with Anik</h3>
                        <ul>
                            <li>
                                <a href="https://www.facebook.com/AnikSingalcom/" target="_blank">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.twitter.com/aniksingal" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.youtube.com/aniksingalcom" target="_blank">
                                    <i class="fa fa-youtube-play"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/singalanik" target="_blank">
                                    <i class="fa fa-instagram"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.linkedin.com/in/aniksingal" target="_blank">
                                    <i class="fa fa-linkedin"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
