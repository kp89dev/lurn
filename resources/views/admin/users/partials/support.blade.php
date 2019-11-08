<div class="col-md-6">
    <!-- BEGIN PORTLET -->
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption caption-md">
                <i class="icon-bar-chart theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase">Customer Support</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="scroller" style="height: 305px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                <div class="general-item-list">
                    @if($support == 'error')
                    <div class="item">
                        <div class="item-head">
                            <em>Error connecting to Desk.com. Try again later. If this problem persists, contact tech support.</em>
                        </div>
                    </div>
                    @elseif( ! count($support->_embedded->entries))
                    <div class="item">
                        <div class="item-head">
                            <em>No Support Activity for this User</em>
                        </div>
                    </div>
                    @else
                    @foreach($support->_embedded->entries as $entry)
                    <div class="item">
                        <div class="item-head">
                            <div class="item-details">
                                <img class="item-pic" src="{{ $user->getPrintableImageUrl() }}">
                                <a href="https://vssmind.desk.com/agent/case/{{$entry->id}}" class="item-name primary-link" target="_blank">View in Desk</a>
                                <span class="item-label">{{ $entry->active_at['carbon']->diffForHumans() }}</span>
                            </div>
                            @if($entry->status == 'open')
                            <span class="item-status"><span class="badge badge-empty badge-danger"></span> Open</span>
                            @elseif($entry->status == 'pending')
                            <span class="item-status"><span class="badge badge-empty badge-danger"></span> Pending</span>
                            @elseif($entry->status == 'closed')
                            <span class="item-status"><span class="badge badge-empty badge-primary"></span> Closed</span>
                            @elseif($entry->status == 'resolved')
                            <span class="item-status"><span class="badge badge-empty badge-primary"></span> Resolved</span>
                            @endif
                        </div>
                        <div class="item-body">
                            {{ $entry->blurb . '&hellip;' }}
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END PORTLET -->
</div>