<div class="col-md-6">
    <!-- BEGIN PORTLET -->
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption caption-md">
                <i class="icon-bar-chart theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase">Purchase History</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-scrollable table-scrollable-borderless">
                <div class="scroller" style="height: 320px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                    <table class="table table-hover table-light">
                        <thead>
                        <tr class="uppercase">
                            <th colspan="2">
                                Course
                            </th>
                            <th>
                                Price
                            </th>
                            <th>
                                Date
                            </th>
                        </tr>
                        </thead>
                        @forelse ($user->courses()->withPivot(['course_infusionsoft_id', 'paid_at', 'cancelled_at', 'cancelled_reason', 'refunded_at', 'cancelled_by', 'status'])->get() as $course)
                            <tr style="{{ ! is_null($course->pivot->cancelled_at) || $course->pivot->status == 1 ? "background-color: #ffe8e8" : "" }}">
                                <td class="fit" colspan="2">
                                    {{ $course->title }}
                                </td>
                                <td>
                                    @php
                                        $refunded = $course->pivot->refunded_at;
                                        $refundAmount = $refunded ? $refunds->where('refunded_at', $course->pivot->refunded_at)->where('course_id', $course->id)->first()->amount : 0.00;
                                        $refundedColor = $refunded ? 'refunded' : '';
                                    @endphp
                                    <span class="bold theme-font {{ $refundedColor }}">
                                        @if($refunded)
                                            $({{ $refundAmount }})
                                        @else
                                            @if (! is_null($course->pivot->course_infusionsoft_id))
                                                @if (is_object($courseInfusionsoft = \App\Models\CourseInfusionsoft::find($course->pivot->course_infusionsoft_id)))
                                                    ${{ $courseInfusionsoft->price }}
                                                @else
                                                    $0,00-
                                                @endif
                                            @else
                                                ${{ number_format($course->infusionsoft->price, 0) }}
                                            @endif
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{ $course->pivot->paid_at ?? $course->pivot->created_at }}
                                </td>
                                <td style="width: 10%">
                                    @if (! is_null($course->pivot->cancelled_at) || $course->pivot->status == 1)
                                        <span class="label label-sm label-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="fit" colspan="4">
                                    <i>No courses</i>
                                </td>
                            </tr>
                        @endforelse

                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END PORTLET -->
</div>
