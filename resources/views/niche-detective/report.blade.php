@extends('layouts.app')

@section('content')
    @php
        if ($modelNiche->location) {
            $modelNicheLocation = json_decode($modelNiche->location);
        }

        if ($modelNiche->top_keywords) {
            $modelNicheTopKeywords = json_decode($modelNiche->top_keywords);
        }

        $colorArray = ['bgm-green', 'bgm-blue', 'bgm-darkblue', 'bgm-purple', 'bgm-yellow'];
        $modelNicheHotProducts = $modelNiche->hot_products ? json_decode($modelNiche->hot_products) : [];
        $topCountryCode = [
            'China'                            => 'CN',
            'India'                            => 'IN',
            'United States'                    => 'US',
            'Indonesia'                        => 'ID',
            'Brazil'                           => 'BR',
            'Pakistan'                         => 'PK',
            'Nigeria'                          => 'NG',
            'Bangladesh'                       => 'BD',
            'Russia'                           => 'RU',
            'Japan'                            => 'JP',
            'Mexico'                           => 'MX',
            'Jamaica'                          => 'JM',
            'Philippines'                      => 'PH',
            'Vietnam'                          => 'VN',
            'Egypt'                            => 'EG',
            'Germany'                          => 'DE',
            'Iran'                             => 'IR',
            'Democratic Republic of the Congo' => 'CG',
            'Turkey'                           => 'TR',
            'France'                           => 'FR',
            'Thailand'                         => 'TH',
            'United Kingdom'                   => 'GB',
            'Italy'                            => 'IT',
            'South Africa'                     => 'ZA',
            'Myanmar'                          => 'MM',
            'Tanzania'                         => 'TZ',
            'South Korea'                      => 'KR',
            'Colombia'                         => 'CO',
            'Spain'                            => 'ES',
            'Sweden'                           => 'SE',
            'Kenya'                            => 'KE',
            'Canada'                           => 'CA',
            'Australia'                        => 'AU',
            'Greece'                           => 'GR'
        ];
    @endphp

    <div class="container">
        <div class="row">
            <div class="col-md-12 niche-design">
                <div class="wrapper">
                    <div class="wrapper">
                        <h1 class="page-title">
                            Niche Report
                            <a href="{{ route('niche-tool')}}" class="btn btn-lg btn-hg btn-primary">Select another
                                Niche</a>
                        </h1>
                        <h2 class="page-tagline">{{ $modelNiche->label }}</h2>

                        <div class="vsep"></div>

                        <div class="niche-report">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row upper-row">
                                        <div class="col-md-6">
                                            <div class="widget widget-products">
                                                <div class="header">
                                                    <h3>Total products</h3>
                                                    <p class="tagline">with a Gravity score of 5+</p>
                                                </div>
                                                <div class="body">
                                                    <div class="icon">
                                                        <i class="fa fa-shopping-cart shopping-icon"></i>
                                                    </div>
                                                    <div class="value">{{ $modelNiche->total_products }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="widget widget-audience">
                                                <div class="header">
                                                    <h3>Audience size</h3>
                                                </div>
                                                <div class="body">
                                                    <div class="icon">
                                                        <i class="fa fa-users users-icon"></i>
                                                    </div>
                                                    <div class="value">{{ $modelNiche->audience_size }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row lower-row">
                                        <div class="col-md-6">
                                            <div class="widget widget-keywords">
                                                <div class="header">
                                                    <h3 style="margin-top: 0px;">Top 5 keywords</h3>
                                                </div>
                                                <div class="body">
                                                    <ul class="top5-keywords" style="margin-left: 0px;">
                                                        @foreach ($modelNicheTopKeywords as $topKeyword)
                                                            <li>{{ $topKeyword }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="widget bottomBox widget-gender">
                                                <div class="header">
                                                    <h3 style="margin-top: 0px;">Gender</h3>
                                                </div>
                                                <div class="body">
                                                    <div id="gender-chart"></div>
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <i class="fa fa-female female-icon"></i>
                                                                <div class="value">
                                                                    {{ $modelNiche->female_percentage }}%
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <i class="fa fa-male male-icon"></i>
                                                                <div class="value">
                                                                    {{ $modelNiche->male_percentage }}%
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="widget widget-bigger widget-map">
                                        <div class="header">
                                            <h3>Location</h3>
                                        </div>
                                        <div class="body">
                                            <div id="location-map" style="height:300px;width: 100%;"></div>
                                            <table class="location-map-info">
                                                <thead>
                                                <tr>
                                                    <th>Top 5 countries</th>
                                                    <!--<th width="30%">Pencent of Visitors</th>-->
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($modelNicheLocation as $key=>$topLocation)
                                                <tr><td>{{ $topLocation->country }}</td></tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget widget-bigger widget-fullpad">
                                <div class="header">
                                    <h3>Hot products: {{ $modelNiche->label }}</h3>
                                </div>
                                <div class="body left-aligned">
                                    @foreach($modelNicheHotProducts as $hotProduct)
                                        <h4>{{ $hotProduct->name }}</h4>

                                        <ul>
                                            @if (trim($hotProduct->benefit1))
                                                <li>{{ $hotProduct->benefit1 }}</li>
                                            @endif
                                            @if (trim($hotProduct->benefit2))
                                                <li>{{ $hotProduct->benefit2 }}</li>
                                            @endif
                                            @if (trim($hotProduct->benefit3))
                                                <li>{{ $hotProduct->benefit3 }}</li>
                                            @endif
                                        </ul>

                                        <div class="vsep-20"></div>

                                        <span style="margin-right: 60px;">{{ $hotProduct->affiliateMarketPlace }}</span>
                                        <a href="{{ $hotProduct->url }}" target="_blank">{{ $hotProduct->url }}</a>
                                        <hr>
                                    @endforeach
                                </div>
                            </div>

                            <div class="widget widget-bigger widget-auf">
                                <div class="header">
                                    <h3>Average upsell funnel</h3>
                                </div>

                                <div class="body upsell-funnel">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h3>Main offer</h3>
                                            <div>Price: ${{ $category->main_offer }}</div>
                                        </div>
                                        <div class="col-md-1">
                                            <i class="fa fa-long-arrow-right"></i>
                                        </div>
                                        <div class="col-md-4">
                                            <h3>Upsell #1</h3>
                                            <div>Price: ${{ $category->upsell1 }}</div>
                                        </div>
                                        <div class="col-md-1">
                                            <i class="fa fa-long-arrow-right"></i>
                                        </div>
                                        <div class="col-md-3">
                                            <h3>Upsell #2</h3>
                                            <div>Price: ${{ $category->upsell2 }}</div>
                                        </div>
                                    </div>
                                    <div class="vsep-30"></div>
                                </div>
                            </div>

                            <div class="vsep-30"></div>

                            <div style="text-align: center;">
                                <a href="{{ route('niche-tool')}}" class="btn btn-lg btn-hg btn-primary">
                                    Select another Niche
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href=" {{ mix('/css/niche.css') }}">
    <style>
        .jvectormap-container {
            height: 100%
        }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/4.1.8/highcharts.src.js"></script>
    <script src="{{ mix('js/niche-detective/libs.js') }}"></script>
    <script>
        function startLoading () {
            $('.loadingImage').show();
            $('.loadingBackground').show();
        }

        function stopLoading () {
            $('.loadingImage').hide();
            $('.loadingBackground').hide();
        }
    </script>

    <script>
        $(function () {
            // Apply the gender chart if necessary.
            if ($('#gender-chart').length) {

                $('#gender-chart').highcharts({
                    chart: {
                        renderTo: 'container',
                        backgroundColor: 'transparent',
                        type: 'pie',
                        margin: [0, 0, 0, 0],
                        spacingTop: 0,
                        spacingBottom: 0,
                        spacingLeft: 0,
                        spacingRight: 0
                    },
                    title: {
                        text: null
                    },
                    tooltip: {
                        enabled: false
                    },
                    plotOptions: {
                        allowPointSelect: false,
                        pie: {
                            dataLabels: {
                                enabled: false
                            },
                            animation: false,
                            states: {
                                hover: {
                                    enabled: false
                                }
                            }
                        }
                    },
                    tooltips: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    colors: ['#3599d4', '#965aa5'],
                    series: [{
                        showInLegend: false,
                        type: 'pie',
                        name: 'Gender',
                        innerSize: '30%',
                        data: [
                            ['Male', {{ $modelNiche->male_percentage }}],
                            ['Female', {{ $modelNiche->female_percentage }}],
                        ]
                    }]
                });
            }

            // Apply the vector map plugin if necessary.
            if ($('#location-map').length) {

                var mapData = {
                    <?php
                    $i = 1;
                    foreach ($modelNicheLocation as $topLocation) {
                        if ($topLocation->country) {
                            echo $topCountryCode[$topLocation->country] . ':' . $i++ . ',';
                        }
                    }
                    ?>
                };

                $('#location-map').vectorMap({
                    backgroundColor: '#eef3f5',
                    zoomButtons: false,
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: '#bec3c8'
                        }
                    },
                    series: {
                        regions: [{
                            values: mapData,
                            scale: ['#19b99a', '#3398de', '#34495e', '#9b58b5', '#f1c40f']
                        }]
                    }
                });
            }

            $('#location-map').vectorMap('get', 'mapObject').updateSize();
        });

    </script>
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
            var min = Math.min.apply( Math, heights );
            var dif = max - min;
            $(selector).each(function() {
                $(this).css('height', max + 'px');
            });
            return dif;
        }

        $(window).ready(function() {
            var addToMap = equalizeHeights($('.niche-report .lower-row .widget'));
            var mapHeight = $('.widget-map').innerHeight();
            $('.widget-map').css('height', mapHeight+addToMap + 'px');
            var iv = null;
            $(window).resize(function() {
                if(iv !== null) {
                    window.clearTimeout(iv);
                }

                iv = setTimeout(function() {
                    equalizeHeights($('.niche-report .lower-row .widget'));
                }, 120);
            }); 
        });
    </script>
@endsection
