@extends('layouts.home')

@section('content')
    <section id="outreach-page">
        <div class="box align center secondary">
            <div class="wide-wrapper card header-callout">
                <h2 class="outreach-alt-color spacer top">Changing How Children In Need Are Educated...</h2>
                <h1 class="outreach-color">Join Us In Our Journey As We Build Schools For <br>Children Who Live In The
                    Slums...</h1>
                <div class="ui three column grid stackable">
                    <div class="column">
                        <img src={{$cdn_url."images/kids-collage.jpg"}} style="width:100%; max-width:290px;" alt="kids collage">
                    </div>
                    <div class="column">
                        <ul class="outreach">
                            <li>Get Pictures, Videos &amp; Regular Updates...</li>
                            <li>Help Us By Simply Spreading The Word... <br>(No Donations)</li>
                            <li>Connect with The Children, Empower Them &amp; See Them Grow!</li>
                            <li>Actively Participate in Growing Our Organization - Even Without Donating!</li>
                        </ul>
                    </div>
                    <div class="column">
                        <div class="optin">
                            <h4>Just <strong><u>Put in Your Primary Email</u></strong> Address &amp; We Will Send You
                                <u>Regular Updates</u> On What Is Happening at Our <strong><u>Dream Centers</u></strong>
                                &amp; How Our Children Are Growing!</h4>
                            <form name="subscriptionFrm_31" id="subscriptionFrm_31"
                                  class="form-horizontal AVAST_PAM_nonloginform"
                                  action="https://lurn.sendlane.com/form/31" method="post">
                                <input name="form_id" id="form_id" value="31" type="hidden">
                                <input class="fis" id="form_field[3]" maxlength="125" name="form_field[3]"
                                       placeholder="Enter Primary Email Here..." type="text">
                                <input name="button" id="button" value="I'II Spread The Word!" type="submit">
                            </form>
                        </div>
                        <p><em>We 100% respect your privacy...</em></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="wrapper">
                <h1 class="title">Help <span class="highlight">Lurn</span> Change The World!</h1>
                <hr class="mt-30">
                <img id="forallourgood-logo" src={{$cdn_url."images/forall-ourgood.png"}} alt="for all our good">
                <h2 class="intro highlight"><strong>For All Our Good</strong> is a self-sustainable, non-profit
                    organization that envisions a world where every child has the opportunity to flourish and unleash
                    their entrepreneurial spirit.</h2>
                <p class="tx large">Started by Anik Singal, CEO and Founder of LURN, Inc., For All Our Good believes
                    that <strong>elevating children is the key to breaking cycles of poverty.</strong> From
                    entrepreneurship comes individual prosperity, and <strong>individual prosperity benefits every
                        community where it thrives.</strong></p>
                <p class="tx large"><strong>The path to prosperity for every child begins with clean water, sound
                        nutrition, electricity and education.</strong> <i>For All Our Good</i> seeks out and supports
                    social entrepreneurs with novel ideas for delivering the essentials of prosperity to children around
                    the globe. By helping other self-sustainable, non-profit organizations become successful and expand,
                    <strong>we can all make the world a better place!</strong></p>
            </div>
        </div>
        <div class="box secondary" style="padding: 0">
            <div class="background-fade-container">
                <div class="background-left" style="background-image: url({{$cdn_url.'images/outreachpage-img2.jpg'}})"></div>
                <div class="fade-box-right">
                    <div class="fade-box-content">
                        <h2 class="tx larger">“We believe in not only teaching someone how to fish, we believe in giving
                            them a few fish while they’re learning.”</h2>
                        <p class="align right">- Anik Singal, Founder <br>For All Our Good</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="box align left secondary">
            <div class="wrapper tx large">
                <p><strong class="highlight">Our process is simple:</strong> raise funds, offer advice and provide
                    networking opportunities for select social entrepreneurs.</p>
                <p><strong class="highlight">Our goal is equally simple:</strong> measurable and sustainable results.
                    Each program supported by For All Our Good has clearly defined objectives, achievable milestones,
                    and a plan to become self-sustaining.</p>
                <p>Ultimately, successful programs can provide templates for other social entrepreneurs to replicate in
                    other places <strong><em>for all our good.</em></strong></p>
            </div>
        </div>


        <div class="box align center arrow-box">
            <div class="wrapper spacer top">

                <h1 class="highlight">Learn More About <em>For All Our Good’s</em> Dream Centre Program</h1>
                <div class="videodiv">
                    <div style="position:relative; padding-top:56.25%; background:#000;">
                        <iframe style="position:absolute; width:100%; height:100%; top:0; left:0;"
                                src="https://www.youtube.com/embed/JdZy2HiEkCg?autoplay=0&amp;controls=0&amp;showinfo=0&amp;rel=0"
                                allowfullscreen="true" width="560" height="315" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
