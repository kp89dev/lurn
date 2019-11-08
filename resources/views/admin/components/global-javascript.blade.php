<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script type="text/javascript" src="/assets/global/plugins/jquery.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/jquery-migrate.min.js"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script type="text/javascript" src="/assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/jquery.blockui.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/jquery.cokie.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<!-- END CORE PLUGINS -->
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
</script>
<script type="text/javascript" src="/assets/global/scripts/metronic.js"></script>
<script type="text/javascript" src="/assets/admin/layout/scripts/layout.js"></script>
<script type="text/javascript" src="/assets/admin/layout/scripts/quick-sidebar.js"></script>
<script type="text/javascript" src="/assets/admin/layout/scripts/demo.js"></script>
<script type="text/javascript" src="{{ mix('js/global.js') }}"></script>

@yield('js')

<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        QuickSidebar.init(); // init quick sidebar
        Demo.init(); // init demo features
    });
</script>
<!-- END JAVASCRIPTS -->
