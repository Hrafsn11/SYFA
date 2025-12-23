<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{-- <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2 me-lg-4 ti-lg"></i>
                    <span class="d-none d-md-inline-block text-muted fw-normal">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search --> --}}

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Home Icon -->
            <li class="nav-item me-2">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill" href="{{ route('home.services') }}" title="Home">
                    <i class="ti ti-home ti-md"></i>
                </a>
            </li>
            <!-- /Home Icon -->

            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class="ti ti-sun me-3"></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon-stars me-3"></i>Dark</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                            <span class="align-middle"><i class="ti ti-device-desktop-analytics me-3"></i>System</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher -->

            <!-- Notification -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <span class="position-relative">
                        <i class="ti ti-bell ti-md"></i>
                        @if (auth()->user()->unread_notifs->count() > 0)
                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                        @endif
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">Notification</h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span id="count_notif" class="badge bg-label-primary me-2">{{ count(auth()->user()->unread_notifs) }}</span>
                                <a href="javascript:void(0)"
                                    id="markAllRead"
                                    class="btn btn-text-secondary rounded-pill btn-icon"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read">
                                    <i class="ti ti-mail-opened text-heading"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @foreach (auth()->user()->notifs->reverse() as $notif)
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <!-- Avatar, if needed
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="/assets/img/avatars/1.png" alt class="rounded-circle" />
                                            </div>
                                        </div> -->
                                        <div class="flex-grow-1">
                                            <a href="/notif-read/{{ $notif->id_notification }}" class="text-black">
                                                <h6 class="mb-1 d-block text-body">{{ strip_tags($notif->content) }}</h6>
                                                <small class="text-muted">{{ $notif->created_at }}</small>
                                            </a>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            @if ($notif->status == 'unread')
                                                <a href="javascript:void(0)" class="dropdown-notifications-read">
                                                    <span class="badge badge-dot bg-primary"></span>
                                                </a>
                                            @endif
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive" data-id="{{ $notif->id }}">
                                                <span class="ti ti-x"></span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="btn btn-primary btn-sm d-flex" href="/notification">
                                <small class="align-middle">Lihat Semua Notifikasi</small>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item mt-0" href="{{ route('profile.show') }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar avatar-online">
                                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="ti ti-settings me-3 ti-md"></i><span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <div class="d-grid px-2 pt-2 pb-1">
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger d-flex w-100">
                                    <small class="align-middle">Logout</small>
                                    <i class="ti ti-logout ms-2 ti-14px"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>

    {{-- <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-xxl border-0" 
            placeholder="Cari halaman, debitur, peminjaman..." 
            aria-label="Search..." 
            autocomplete="off" />
        <i class="ti ti-x search-toggler cursor-pointer"></i>
    </div> --}}
</nav>

<script>
    // Initialize navbar dropdowns after navbar is rendered
    (function() {
        function initNavbarDropdowns() {
            if (typeof bootstrap === 'undefined') {
                setTimeout(initNavbarDropdowns, 50);
                return;
            }
            
            // Re-initialize Perfect Scrollbar for navbar dropdowns
            if (window.Helpers && typeof window.Helpers.initNavbarDropdownScrollbar === 'function') {
                window.Helpers.initNavbarDropdownScrollbar();
            }
            
            // Initialize all dropdowns in navbar
            const navbarElement = document.getElementById('layout-navbar');
            if (!navbarElement) return;
            
            const dropdownTriggers = navbarElement.querySelectorAll('[data-bs-toggle="dropdown"]');
            
            dropdownTriggers.forEach((trigger) => {
                // Dispose any existing dropdown instance
                const existingInstance = bootstrap.Dropdown.getInstance(trigger);
                if (existingInstance) {
                    existingInstance.dispose();
                }
                
                // Create new dropdown instance with options
                new bootstrap.Dropdown(trigger, {
                    autoClose: true,
                    boundary: 'window'
                });
            });
            
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNavbarDropdowns);
        } else {
            initNavbarDropdowns();
        }
        
        // Re-initialize on Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initNavbarDropdowns, 100);
        });
    })();

    // // Global Search with Typeahead (menggunakan struktur Vuexy template)
    // (function() {
    //     if (typeof $ === 'undefined' || typeof $.fn.typeahead === 'undefined') {
    //         console.warn('jQuery or Typeahead.js not loaded');
    //         return;
    //     }

    //     var searchInput = $('.search-input'),
    //         searchInputWrapper = $('.search-input-wrapper'),
    //         contentBackdrop = $('.content-backdrop');

    //     if (!searchInput.length) return;

    //     // Filter config untuk typeahead
    //     var filterConfig = function(data) {
    //         return function findMatches(q, cb) {
    //             var matches = [];
    //             data.filter(function(i) {
    //                 if (i.name.toLowerCase().includes(q.toLowerCase())) {
    //                     matches.push(i);
    //                 }
    //             });
    //             cb(matches);
    //         };
    //     };

    //     // Fetch search data dari API
    //     function fetchSearchData(query, cb) {
    //         $.ajax({
    //             url: '{{ route("search.api") }}',
    //             data: { q: query },
    //             dataType: 'json',
    //             success: function(data) {
    //                 cb(data);
    //             },
    //             error: function() {
    //                 cb({ pages: [], debitur: [], peminjaman: [], investasi: [] });
    //             }
    //         });
    //     }

    //     // Init typeahead
    //     searchInput.typeahead(
    //         {
    //             hint: false,
    //             classNames: {
    //                 menu: 'tt-menu navbar-search-suggestion',
    //                 cursor: 'active',
    //                 suggestion: 'suggestion d-flex justify-content-between px-4 py-2 w-100'
    //             }
    //         },
    //         // Pages
    //         {
    //             name: 'pages',
    //             display: 'name',
    //             limit: 5,
    //             async: true,
    //             source: function(query, syncResults, asyncResults) {
    //                 fetchSearchData(query, function(data) {
    //                     asyncResults(data.pages || []);
    //                 });
    //             },
    //             templates: {
    //                 header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Pages</h6>',
    //                 suggestion: function(item) {
    //                     return '<a href="' + item.url + '">' +
    //                         '<div>' +
    //                         '<i class="ti ' + item.icon + ' me-2"></i>' +
    //                         '<span class="align-middle">' + item.name + '</span>' +
    //                         '</div>' +
    //                         '</a>';
    //                 },
    //                 notFound: '<div class="not-found px-4 py-2">' +
    //                     '<h6 class="suggestions-header text-primary mb-2">Pages</h6>' +
    //                     '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> Tidak ada halaman</p>' +
    //                     '</div>'
    //             }
    //         },
    //         // Debitur & Investor
    //         {
    //             name: 'debitur',
    //             display: 'name',
    //             limit: 4,
    //             async: true,
    //             source: function(query, syncResults, asyncResults) {
    //                 fetchSearchData(query, function(data) {
    //                     asyncResults(data.debitur || []);
    //                 });
    //             },
    //             templates: {
    //                 header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Debitur & Investor</h6>',
    //                 suggestion: function(item) {
    //                     var subtitle = item.subtitle ? '<small class="text-muted">' + item.subtitle + '</small>' : '';
    //                     return '<a href="' + item.url + '">' +
    //                         '<div class="d-flex align-items-center w-100">' +
    //                         '<i class="ti ti-users me-3"></i>' +
    //                         '<div class="w-100">' +
    //                         '<h6 class="mb-0">' + item.name + '</h6>' +
    //                         subtitle +
    //                         '</div>' +
    //                         '</div>' +
    //                         '</a>';
    //                 },
    //                 notFound: '<div class="not-found px-4 py-2">' +
    //                     '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> Tidak ada debitur</p>' +
    //                     '</div>'
    //             }
    //         },
    //         // Peminjaman
    //         {
    //             name: 'peminjaman',
    //             display: 'name',
    //             limit: 4,
    //             async: true,
    //             source: function(query, syncResults, asyncResults) {
    //                 fetchSearchData(query, function(data) {
    //                     var combined = (data.pengajuan_peminjaman || []).concat(data.sfinlog_peminjaman || []);
    //                     asyncResults(combined);
    //                 });
    //             },
    //             templates: {
    //                 header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Peminjaman</h6>',
    //                 suggestion: function(item) {
    //                     var subtitle = item.subtitle ? '<small class="text-muted">' + item.subtitle + '</small>' : '';
    //                     return '<a href="' + item.url + '">' +
    //                         '<div class="d-flex align-items-center w-100">' +
    //                         '<i class="ti ti-briefcase me-3"></i>' +
    //                         '<div class="w-100">' +
    //                         '<h6 class="mb-0">' + item.name + '</h6>' +
    //                         subtitle +
    //                         '</div>' +
    //                         '</div>' +
    //                         '</a>';
    //                 }
    //             }
    //         },
    //         // Investasi
    //         {
    //             name: 'investasi',
    //             display: 'name',
    //             limit: 4,
    //             async: true,
    //             source: function(query, syncResults, asyncResults) {
    //                 fetchSearchData(query, function(data) {
    //                     asyncResults(data.pengajuan_investasi || []);
    //                 });
    //             },
    //             templates: {
    //                 header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Investasi</h6>',
    //                 suggestion: function(item) {
    //                     var subtitle = item.subtitle ? '<small class="text-muted">' + item.subtitle + '</small>' : '';
    //                     return '<a href="' + item.url + '">' +
    //                         '<div class="d-flex align-items-center w-100">' +
    //                         '<i class="ti ti-coin me-3"></i>' +
    //                         '<div class="w-100">' +
    //                         '<h6 class="mb-0">' + item.name + '</h6>' +
    //                         subtitle +
    //                         '</div>' +
    //                         '</div>' +
    //                         '</a>';
    //                 }
    //             }
    //         }
    //     )
    //     .bind('typeahead:render', function() {
    //         contentBackdrop.addClass('show').removeClass('fade');
    //     })
    //     .bind('typeahead:select', function(ev, suggestion) {
    //         if (suggestion.url) {
    //             window.location = suggestion.url;
    //         }
    //     })
    //     .bind('typeahead:close', function() {
    //         searchInput.val('');
    //         searchInput.typeahead('val', '');
    //         searchInputWrapper.addClass('d-none');
    //         contentBackdrop.addClass('fade').removeClass('show');
    //     });

    //     // Handle keyup untuk backdrop
    //     searchInput.on('keyup', function() {
    //         if (searchInput.val() == '') {
    //             contentBackdrop.addClass('fade').removeClass('show');
    //         }
    //     });

    //     // Init PerfectScrollbar
    //     var psSearch;
    //     $('.navbar-search-suggestion').each(function() {
    //         psSearch = new PerfectScrollbar($(this)[0], {
    //             wheelPropagation: false,
    //             suppressScrollX: true
    //         });
    //     });

    //     searchInput.on('keyup', function() {
    //         if (psSearch) psSearch.update();
    //     });
    // })();
</script>
