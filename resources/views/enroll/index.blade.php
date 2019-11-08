@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="padded-twice shadow">
            <h1><i class="university icon"></i> Enrollment Form</h1>
            <p>Excellent choice! Please fill in the form below to finish placing your order.</p>

            <hr />

            <form id="enroll" class="ui form" action="{{ route('do.enrollment', $course->slug) }}" method="POST">
                {{ csrf_field() }}

                <table id="cart-item">
                    <tr>
                        <th colspan="2">Course</th>
                        @if ($course->infusionsoft->subscription)
                            <th>Payment Type</th>
                        @endif
                        <th>Total Now</th>
                    </tr>
                    <tr class="info">
                        <td class="image">
                            <img src="{{ $course->getPrintableImageUrl() }}">
                        </td>
                        <td class="title">
                            @if ($course->categoryList->count())
                                <p>{{ $course->categoryList->implode(', ') }}</p>
                            @endif
                            <h2>{{ $course->title}}</h2>
                        </td>
                        @if ($course->infusionsoft->subscription)
                            <td class="payment-type">
                                <div>
                                    @if ($course->infusionsoft->is_product_id)
                                        <div>
                                            <div class="ui radio checkbox">
                                                <input type="radio" name="payment_type" value="full"  {{ in_array(old('payment_type'), [null, 'full'], false) ? 'CHECKED' : '' }}
                                                       onchange="updateTotal('{{ number_format($course->price, 2) }}')">
                                                <label>
                                                    One Payment<br>
                                                    <span class="value">${{ number_format($course->price, 2) }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="payment_type" value="subscription" {{ old('payment_type') == 'subscription' ? 'CHECKED' : '' }}
                                                   onchange="updateTotal('{{ number_format($course->infusionsoft->subscription_price, 2) }}')">
                                            <label>
                                                {{ $course->infusionsoft->payments_required or '' }}
                                                Monthly Payments<br>
                                                <span class="value">
                                                    ${{ number_format($course->infusionsoft->subscription_price, 2) }} / month
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                        <td class="course-price">
                            ${{ in_array(old('payment_type'), [null, 'full'], false) ? number_format($course->price, 2) : number_format($course->infusionsoft->subscription_price, 2)}}
                        </td>
                    </tr>
                </table>

                <hr>

                <div class="ui two columns grid">
                    <div class="column credit-cards {{ count($cards) ? '' : 'faded' }}">
                        <h3>Your Credit Cards</h3>
                        <p>Click to select one</p>

                        @if (count($cards))
                            @foreach ($cards as $card)
                                <div class="credit-card">
                                    <i class="{{ get_payment_icon_name($card['CardType']) }} type icon"></i>
                                    <i class="check icon"></i>

                                    <div class="name">{{ $card['NameOnCard'] }}</div>
                                    <div class="meta">
                                        <span class="number">
                                            <span class="faded">•••• •••• ••••</span>
                                            {{ $card['Last4'] }}
                                        </span>
                                        <span class="exp-date">
                                            {{ $card['ExpirationMonth'] }}/{{ $card['ExpirationYear'] }}
                                        </span>
                                    </div>

                                    <input type="radio" name="saved_credit_card" value="{{ json_encode($card) }}">
                                </div>
                            @endforeach
                        @else
                            <p>You currently don't have any credit cards saved.</p>
                        @endif

                        <div class="add-new credit-card">
                            <i class="plus icon"></i>
                            <span>Add a New Credit Card</span>
                        </div>
                    </div>

                    <div id="add-new-card" class="column {{ count($cards) ? '' : 'visible' }}">
                        <h3>Add a New Credit Card - <a href="javascript:;" id="hide-new-card">Cancel</a></h3>
                        <p>Your Information</p>

                        <div class="card-container"></div>
                        <div class="ui grid">
                            <div class="eleven wide column">
                                <div class="field">
                                    <label>Card Number</label>
                                    <input type="text" name="card[number]" placeholder="1234 1234 1234 1234">
                                </div>
                            </div>
                            <div class="five wide column">
                                <div class="field">
                                    <label>Expiration Date</label>
                                    <input type="text" name="card[expDate]" placeholder="{{ date('m/Y') }}">
                                </div>
                            </div>
                            <div class="eleven wide column">
                                <div class="field">
                                    <label>Name on Card</label>
                                    <input type="text" name="card[nameOnCard]" placeholder="John Smith">
                                </div>
                            </div>
                            <div class="five wide column">
                                <div class="field">
                                    <label>CVV Number</label>
                                    <input type="text" name="card[cvv]" placeholder="123">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="center aligned mt-30">
                    <button class="ui secondary left labeled icon button">
                        <i class="check icon"></i>
                        <strong>Enroll</strong>
                    </button>
                </div>

                <input type="hidden" name="token" value="{{ $token }}"/>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/card/2.3.0/card.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/checkbox.min.js"></script>
    <script type="text/javascript">
        window.cartSaved = false;
        
        function updateTotal (value) {
            $('.course-price').text('$' + value);
        }

        function saveCartReminder (e) {
            if (window.cartSaved) {
                return false;
            }

            $.post('/api/save-cart-reminder', {
                'course_id': {{ $course->id }},
                'user_id': {{ user()->id }}
            });

            window.cartSaved = true;
        }

        function removeCartReminder (e) {
            if (window.cartSaved) {
                $.post('/api/remove-cart-reminder', {
                    'course_id': {{ $course->id }},
                    'user_id': {{ user()->id }}
                });
            }
        }

        $(function () {
            $('.ui.checkbox').each(function () {
                $(this).checkbox();
            });

            $('.credit-card').not('.add-new').on('click', function (e) {
                var card = $(this);

                card.find('input').prop('checked', true);

                $('.credit-card.selected').removeClass('selected');
                card.addClass('selected');

                $('#add-new-card').removeClass('visible');
                $('.credit-cards').removeClass('faded');
            });

            $('.credit-card.add-new').on('click', function () {
                $('#add-new-card').addClass('visible');
                $('.credit-cards').addClass('faded');
            });

            $('#hide-new-card').on('click', function () {
                $('#add-new-card').removeClass('visible');
                $('.credit-cards').removeClass('faded');
            });

            $('input[name="card[number]"]').on('change', saveCartReminder);
            $('.credit-card, input[name="saved_credit_card"]').on('click', saveCartReminder);
            $('#enroll').on('submit', removeCartReminder);

            var card = new Card({
                form: 'form#enroll',
                container: '.card-container',
                width: 300,

                formSelectors: {
                    numberInput: 'input[name="card[number]"]',
                    expiryInput: 'input[name="card[expDate]"]',
                    cvcInput: 'input[name="card[cvv]"]',
                    nameInput: 'input[name="card[nameOnCard]"]'
                },

                formatting: true,

                messages: {
                    validDate: 'valid\ndate',
                    monthYear: 'mm/yyyy',
                },

                placeholders: {
                    number: '•••• •••• •••• ••••',
                    name: 'John Smith',
                    expiry: '••/••',
                    cvc: '•••'
                },
            });
        });
    </script>
@endsection
