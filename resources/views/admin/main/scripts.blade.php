    <!-- Vendor js -->
    <script src="{{ asset('dashboard/assets/js/vendors.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('dashboard/assets/js/app.js') }}"></script>
    @if (request()->routeIs('dashboard'))
    <script src="{{ asset('dashboard/assets/js/pages/dashboard.js') }}"></script>
    @endif
    <!-- Choises.js Plugin Js -->
    <script src="{{ asset('dashboard/assets/plugins/choices/choices.min.js') }}"></script>
    <!-- Choices Demo Js-->
    <script src="{{ asset('dashboard/assets/js/pages/form-choice.js') }}"></script>
    <script src="{{asset('dashboard/assets/plugins/fullcalendar/index.global.min.js')  }}"></script>
    @include('admin.main.messages')

