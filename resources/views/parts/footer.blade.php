<div id="footer">
    <div class="top wrapper padded-twice">
        <div class="ui grid">
            <div class="four wide column">
                <img src="{{ asset('images/logo.svg') }}" alt="Lurn Nation Logo" style="height: 40px">
                <p>Lurn.com is the best source for all things digital publishing. On this site, you’ll discover the best advice from some of the most successful digital publishers in the world.</p>
            </div>
            <div class="four wide column">
                <h3><strong>Get Involved</strong></h3>
                <ul>
                    <li><a href="{{ url('careers') }}">Careers</a></li>
                    <li><a href="{{ url('outreach') }}">Outreach</a></li>
                </ul>
            </div>
            <div class="four wide column">
                <h3><strong>Keep Learning</strong></h3>
                <ul>
                    <li><a href="{{ url('about') }}">About Us</a></li>
                    <li><a href="{{ url('blog') }}">Blog</a></li>
                </ul>
            </div>
            <div class="four wide column">
                <h3><strong>Get in Touch</strong></h3>
                <ul>
                    <li class="with-icon">
                        <i class="map marker alternate icon"></i>
                        2098 Gaither Road<br>Gaithersburg, MD 20850
                    </li>
                    <li class="with-icon">
                        <i class="phone flipped icon"></i>
                        (888) 477 9719<br>Extension 2
                    </li>
                    <li class="with-icon">
                        <i class="envelope icon"></i>
                        <a href="mailto:support@lurn.com">support@lurn.com</a>
                    </li>
                    <li class="with-icon">
                        <i class="globe icon"></i>
                        <a href="{{ url('') }}">www.lurn.com</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bottom aligned center padded">
        Copyright &copy; {{ date('Y') }}
        <a href="{{ url('') }}">Lurn, Inc.</a>
    </div>
</div>