<!-- jQuery first -->
<script src="{{ asset('admin/js/jquery.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('admin/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>

<!-- Charts only on dashboard -->
@if(request()->is('dashboard') || request()->is('*/dashboard'))
<script src="{{ asset('admin/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('admin/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('admin/plugins/chartjs/js/chart.js') }}"></script>
@endif

<!-- DataTable -->
<script src="{{ asset('admin/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($('#example').length) {
            $('#example').DataTable();
        }
        if ($('#example2').length) {
            // The DataTables Buttons extension is not bundled in
            // public/admin/plugins/datatable, so the previous `buttons: [...]`
            // config plus `table.buttons()` threw "table.buttons is not a
            // function" and aborted this handler on every records page.
            $('#example2').DataTable({
                lengthChange: false,
                pageLength: 25,
                order: []
            });
        }
    });
</script>

<!-- App JS -->
<script src="{{ asset('admin/js/app.js') }}"></script>

<script>
    if (document.querySelector('.app-container')) {
        new PerfectScrollbar(".app-container");
    }
</script>
