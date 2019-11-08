<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lurn Nation Sitemap Index</title>
        <meta name="description" content="Lurn Nation Sitemap Index">
        <meta name="robots" content="index, follow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:100,300,900|Pangolin">
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/css/iziToast.min.css">
        <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">


        <style>
            body {
                color: #f7f8fa;
            }
            h1 {
                color: #444;
            }
        </style>

        <script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/vue/2.2.4/vue.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/js/iziToast.min.js"></script>
        <script type="text/javascript" src="{{ mix('js/helpers.js') }}"></script>

        @if (env('APP_ENV') != 'testing' && !session()->has('admin_impersonator'))
        @include('woopra.index')
        @endif

        <script type="text/javascript" src="{{ mix('js/track.js') }}"></script>
    </head>
    <body>
        <div id="header">
            <div class="wrapper">
                <table>
                    <tr>
                        <td id="logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ url('images/logo.svg') }}" alt="Lurn Central Logo">
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="wrapper">
            <div class="shadow">
                <div id="content" class="padded-twice">
                    <h1>Lurn Nation Sitemap Index</h1>

                    <hr>
                    {!! $index !!}
                </div>
            </div>
        </div>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/transition.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dropdown.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dimmer.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/modal.min.js"></script>
        <script type="text/javascript" src="{{ mix('js/dynamic-modal.js') }}"></script>
        <script type="text/javascript">
            $('sitemap, url').on('click', function(){
                var where = $(this).children('loc').html();
                window.location = where;
            });
        </script>
    </body>
</html>