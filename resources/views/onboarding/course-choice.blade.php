@extends('layouts.chute')

@section('content')
    <form action="{{ route('onboarding.enroll') }}" method="POST">
        @csrf
        <div class="ui grid container">
            <div class="column sixteen wide" style="padding-right:2rem; padding-left:2rem;">
                <h1>Here Are The Top FREE Courses for<br>{{ $category->name }}&hellip;</h1>
                <p style="margin-left:1rem;">
                    Please select all of the free courses that you are interested in. (Click anywhere in the box.)
                </p>
                <p style="margin-left:1rem;">Then, click Next Step.</p>
            </div>

            @foreach ($possibleCourses as $course)
                <div class="five wide column course-column">
                    <div class="ui card" style="margin: auto">
                        <div class="image">
                            <img src="{{ $course->getPrintableImageUrl() }}">
                        </div>
                        <div class="content">
                            <a class="title" href="javascript:;">
                                {{ $course->title }}
                            </a>
                            <div class="description tx accented spacer top" style="height: 8.5em">
                                <p class="tx light course-snippet">
                                    {!! $course->snippet !!}
                                </p>
                            </div>
                        </div>
                        <div class="extra content">
                            <table>
                                <tr>
                                    <td>
                                        <i class="fa fa-user icon-circle-small"></i>
                                        {{ number_format($course->getCounters()->students) }}
                                    </td>
                                    <td width="50%">
                                        <i class="fa fa-thumbs-up"></i>
                                        {{ number_format($course->getCounters()->likes)  }}

                                        <i class="picked fa fa-check" data-course-id="{{ $course->id }}" style="display:none;"></i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="five column row" style="padding:3rem 1rem 1rem 1rem;">
                <div class="eight wide column" style="padding-left:1rem;">
                    <a href="{{ route('onboarding.index') }}" class="ui button muted">Go Back</a>
                </div>
                <div class="eight wide right aligned column" style="padding-left:1rem;">
                    <button type="submit" class="ui next button">
                        Next Step
                    </button>
                </div>
            </div>
        </div>

        <div id="selected-courses"></div>
    </form>

    <style>
        #confirm-dialog {
            background-color: rgba(0, 0, 0, .5);
            display: none;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 99999;
        }

        #confirm-dialog .dialog {
            background-color: #fff;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            font-weight: normal;
            border-radius: 3px;
            box-shadow: 0 10px 30px 0 rgba(0, 0, 0, .2);
        }

        #confirm-dialog .actions {
            text-align: center;
            margin-top: 20px;
        }

        #confirm-dialog .actions button {
            background-color: #bbb;
            border-radius: 100px;
            padding: 12px 30px 10px;
            border: none;
            color: #1a3e6f;
            text-transform: uppercase;
            cursor: pointer;
            transition: all .2s;
            line-height: 20px;
            font-weight: normal;
            font-family: gt_walsheim_promedium, sans-serif;
        }

        #confirm-dialog .actions button:hover {
            background-color: #ddd;
        }

        #confirm-dialog .actions button.confirm {
            background-color: #ebc71d;
            margin-right: 10px;
        }

        #confirm-dialog .actions button.confirm:hover {
            background-color: #ffd81f;
        }
    </style>

    <div id="confirm-dialog">
        <div class="dialog">
            <div class="message"></div>
            <div class="actions">
                <button type="button" onclick="confirmedDialog()" class="confirm"></button>
                <button type="button" onclick="deniedDialog()" class="deny"></button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function refreshSelectedCourses () {
                var selectedCourses = '';

                $('.card .picked:visible').each(function () {
                    selectedCourses += '<input type="hidden" name="courses[]" value="' + $(this).data('course-id') + '">';
                });

                $('#selected-courses').html(selectedCourses);
            }

            $('.card').click(function () {
                $(this).find('.picked').toggle();
                refreshSelectedCourses();
            });

            var dialog = $('#confirm-dialog');

            function confirmDialog (message, confirmText, denyText) {
                dialog.find('.message').html(message);
                dialog.find('.confirm').html(confirmText || 'Yes');
                dialog.find('.deny').html(denyText || 'No');
                dialog.css('display', 'flex');
            }

            window.confirmedDialog = function () {
                window.location = '/onboarding/demo';
            };

            window.deniedDialog = function () {
                dialog.css('display', 'none');
            };

            $('form').on('submit', function () {
                if (! $('.card .picked:visible').length) {
                    confirmDialog("Are you sure that you don't want to select any courses?", 'Yes', 'No - Go Back');
                    return false;
                }
            });
        });
    </script>
@endsection
