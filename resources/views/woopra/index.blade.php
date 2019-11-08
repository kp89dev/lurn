<script>
    !function(){var a,b,c,d=window,e=document,f=arguments,g="script",h=["config","track","trackForm","trackClick","identify","visit","push","call"],i=function(){var a,b=this,c=function(a){b[a]=function(){return b._e.push([a].concat(Array.prototype.slice.call(arguments,0))),b}};for(b._e=[],a=0;a<h.length;a++)c(h[a])};for(d.__woo=d.__woo||{},a=0;a<f.length;a++)d.__woo[f[a]]=d[f[a]]=d[f[a]]||new i;b=e.createElement(g),b.async=1,b.src="//static.woopra.com/js/w.js",c=e.getElementsByTagName(g)[0],c.parentNode.insertBefore(b,c)}("woopra");

    woopra.config({
        domain: "lurn.com"
    });

    @if (user())
        woopra.identify({
            email: '{{ user()->email }}',
            name: '{{ user()->name }}',
            id: '{{ user()->id }}',
            avatar: '{{ user()->getPrintableImageUrl() }}'
        });
    @endif

    woopra.track('pv', {
        url: window.location.pathname,
        title: '@yield("title")'
    });
    
    $(document).ready(function(){
        if ($('#featured-course-widget').length > 0 ){
            woopra.track("featuredCourse", {
                url: 'none',
                title: 'Featured Course Widget',
                action: 'viewed',
                viewedURL: window.location.href
            })
        }
    });
    
    //all outbound links should include class 'woopra-outbound'
    $(document).on('click', '.woopra-outbound', function(){
        var $this = $(this);
        woopra.track("outgoing", {
            url: $(this).attr("href"),
            link: $(this).text()
        });
    });
    
    var woopraFunctions = {
        pushNotification: function (pushNotificationResponse) {
            var action = pushNotificationResponse.data('woopra').action;
            var toURL = pushNotificationResponse.attr("href");
            if (action == 'close'){
                toURL = 'none';
            }
            woopra.track("pushNotification", {
                toURL: toURL,
                title: pushNotificationResponse.data('title'),
                action: action,
                viewedURL: window.location.href
            });
        },
        featuredCourse: function (featuredCourseResponse) {
            woopra.track("featuredCourse", {
                url: featuredCourseResponse.attr("href"),
                title: featuredCourseResponse.data('woopra').title,
                action: featuredCourseResponse.data('woopra').action,
                viewedURL: window.location.href
            });
        },
        userDashAd: function (userDashAdResponse) {
            woopra.track("userDashAd", {
                url: userDashAdResponse.attr("href"),
                title: userDashAdResponse.data('woopra').title,
                action: userDashAdResponse.data('woopra').action
            });
        },
        courseForum: function (courseForumResponse) {
            woopra.track("courseForum", {
                title: courseForumResponse.data('woopra').title,
                action: courseForumResponse.data('woopra').action
            });
        }
    };
    
    $(document).on('click', '.woopra-track', function() {
        var $this = $(this);
        var details = $this.data('woopra');
        woopraFunctions[details.type]($this);
    });

    /**
     * @type {*|Tracker}
     */
    (function () {
        var i,
                s,
                z,
                w = window,
                d = document,
                a = arguments,
                q = 'script',
                f = ['config', 'track', 'identify', 'visit', 'trackClick', 'trackForm', 'push', 'call'],
                c = function () {
                    var i, self = this;
                    self._e = [];
                    for (i = 0; i < f.length; i++) {
                        (function (f) {
                            self[f] = function () {
                                // need to do this so params get called properly
                                self._e.push([f].concat(Array.prototype.slice.call(arguments, 0)));
                                return self;
                            };
                        })(f[i]);
                    }
                };
        w._w = w._w || {};
        // check if instance of tracker exists
        for (i = 0; i < a.length; i++) {
            w._w[a[i]] = w[a[i]] = w[a[i]] || new c();
        }
        // insert tracker script
        s = d.createElement(q);
        s.async = 1;
        s.src = '{{ mix('/js/tracker.js') }}';
        z = d.getElementsByTagName(q)[0];
        z.parentNode.insertBefore(s, z);
    })('lurnTracker');


    lurnTracker.config({
        domain: "{{ env('APP_URL') }}",
        ping: false,
        hide_campaign: true
    });
    @if (user())
        lurnTracker.identify({
            email: '{{ user()->email }}',
            id: '{{ user()->id }}'
        });
    @endif

    lurnTracker.track('pv', {
        url: window.location.pathname,
        title: '@yield("title")'
    });
</script>
