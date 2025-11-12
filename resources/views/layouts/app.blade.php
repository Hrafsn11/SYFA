<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SYFA') }}</title>

    @assets
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    @endassets

    <!-- Helpers -->

    <!-- Adds the Core Table Styles -->
    @rappasoftTableStyles

    <!-- Adds any relevant Third-Party Styles (Used for DateRangeFilter (Flatpickr) and NumberRangeFilter) -->
    @rappasoftTableThirdPartyStyles

    <!-- Styles -->
    @livewireStyles
    <!-- Page CSS -->
    @stack('styles')

    <!-- Scripts -->
    {{-- @vite([ 'resources/js/app.js']) --}}

    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Readonly Styles -->
    <style>
        /* Make readonly text inputs and textareas look like disabled elements */
        input[type="text"][readonly], 
        input[type="email"][readonly], 
        input[type="password"][readonly], 
        input[type="number"][readonly], 
        input[type="tel"][readonly], 
        input[type="url"][readonly],
        textarea[readonly] {
            background-color: #f3f2f3 !important;
            color: #6c757d !important;
            cursor: default !important;
            border-color: #ced4da !important;
            color: #acaab1 !important;
        }
        
        /* Readonly form controls with Bootstrap classes for text inputs and textareas */
        .form-control[readonly] {
            background-color: #f3f2f3 !important;
            color: #acaab1 !important;
            cursor: default !important;
            border-color: #ced4da !important;
        }
        
        /* Input groups with readonly text inputs */
        .input-group .form-control[readonly] {
            background-color: #f3f2f3 !important;
            border-color: #ced4da !important;
        }
        
        /* Input group text for readonly inputs */
        .input-group .form-control[readonly] ~ .input-group-text {
            background-color: #f3f2f3 !important;
            color: #acaab1 !important;
            border-color: #ced4da !important;
        }
        
        /* Flatpickr readonly inputs */
        .flatpickr-input[readonly] {
            background-color: #f3f2f3 !important;
            color: #acaab1 !important;
            cursor: default !important;
            border-color: #ced4da !important;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('partials.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('partials.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @hasSection('content')
                            @yield('content')
                        @elseif(isset($slot))
                            {{ $slot }}
                        @endif
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('partials.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    @stack('modals')

    {{-- ✅ CRITICAL: Livewire Scripts HARUS sebelum Rappasoft --}}
    @livewireScriptConfig
    
    {{-- ✅ Rappasoft Table Scripts (butuh Livewire & Alpine sudah loaded) --}}
    @rappasoftTableScripts
    @rappasoftTableThirdPartyScripts
    
    @assets
    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    @stack('vendor-scripts')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Flat Picker -->
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>


    <!-- Form Validation -->
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-validation.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @endassets
    <!-- Main JS -->

    <script src="{{ asset('assets/vendor/js/content.js') }}"></script>
    

    <!-- Cleave.js Rupiah Helper -->
    <script>
        // Global function untuk inisialisasi Cleave.js pada input rupiah
        window.initCleaveRupiah = function() {
            // Selector untuk semua input yang perlu format rupiah
            const rupiahInputs = document.querySelectorAll('.input-rupiah, [data-format="rupiah"]');

            rupiahInputs.forEach(function(input) {
                // Skip jika sudah di-initialize
                if (input.dataset.cleaveInitialized === 'true') {
                    return;
                }

                // Initialize Cleave.js
                new Cleave(input, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalScale: 0,
                    prefix: 'Rp ',
                    rawValueTrimPrefix: true,
                    noImmediatePrefix: false
                });

                // Mark as initialized
                input.dataset.cleaveInitialized = 'true';
            });
        };

        // Function untuk get raw value (angka saja tanpa format)
        window.getCleaveRawValue = function(element) {
            if (!element) return 0;
            const value = element.value.replace(/[^0-9]/g, '');
            return parseInt(value) || 0;
        };

        // Function untuk set value dengan format rupiah
        window.setCleaveValue = function(element, value) {
            if (!element) return;
            // Remove existing Cleave instance if any
            if (element._vCleave) {
                element._vCleave.destroy();
            }
            // Set value
            element.value = value;
            // Reinitialize
            window.initCleaveRupiah();
        };
    </script>

    <script type="module">
        document.addEventListener('livewire:navigated', () => {            
            window.Helpers.destroy();
            window.Helpers.init();
            // Update layout after page load
            if (document.readyState === 'complete') Helpers.update();else document.addEventListener('DOMContentLoaded', function onContentLoaded() {
                Helpers.update();
                document.removeEventListener('DOMContentLoaded', onContentLoaded);
            });

            window.initVuexy?.();
        });
    </script>    
    {{-- Custom page scripts --}}
    @stack('scripts')
</body>

</html>
