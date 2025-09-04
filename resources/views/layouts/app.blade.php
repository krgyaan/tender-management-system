<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>@yield('page-title') | Volks Energie</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coinex.css') }}" id="theme-style">
    <link rel="stylesheet" href="{{ asset('assets/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2-4.1.0/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/DataTables/datatables.min.css') }}">
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">-->
    <link rel="stylesheet" href="{{ asset('assets/css/filepond.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/filepond-img-preview.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ckeditor.css') }}">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.0.0/ckeditor5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    @stack('styles')
    <script>
        const ASSET_BASE = "{{ asset('assets/css') }}/";
    </script>
</head>

<body class="light-mode">
    <aside class="sidebar sidebar-default navs-rounded ">
        <div class="sidebar-header d-flex align-items-center justify-content-start">
            <a href="{{ route('dashboard') }}" class="navbar-brand dis-none align-items-center justify-content-center">
                <img src="{{ asset('assets/images/ve_logo_2.png') }}" alt="VolksEnergies Logo" width="40px">
                <h5 class="logo-title m-0">&nbsp; Volks Energie</h5>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="2"></path>
                        <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor"
                            stroke-width="2">
                        </path>
                    </svg>
                </i>
            </div>
        </div>
        @php
            $user = Auth::user();
            $permissions = explode(',', $user->permissions);
        @endphp
        <div class="sidebar-body p-0 data-scrollbar">
            <div class="collapse navbar-collapse pe-3" id="sidebar">
                <ul class="navbar-nav iq-main-menu">
                    <li class="nav-item ">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('dashboard') }}">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.15722 20.7714V17.7047C9.1572 16.9246 9.79312 16.2908 10.581 16.2856H13.4671C14.2587 16.2856 14.9005 16.9209 14.9005 17.7047V17.7047V20.7809C14.9003 21.4432 15.4343 21.9845 16.103 22H18.0271C19.9451 22 21.5 20.4607 21.5 18.5618V18.5618V9.83784C21.4898 9.09083 21.1355 8.38935 20.538 7.93303L13.9577 2.6853C12.8049 1.77157 11.1662 1.77157 10.0134 2.6853L3.46203 7.94256C2.86226 8.39702 2.50739 9.09967 2.5 9.84736V18.5618C2.5 20.4607 4.05488 22 5.97291 22H7.89696C8.58235 22 9.13797 21.4499 9.13797 20.7714V20.7714"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                            </i>
                            <span class="item-name">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }} "
                            data-bs-toggle="collapse" href="#sidebar-tender" role="button" aria-expanded="false"
                            aria-controls="sidebar-tender">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M3 6.5C3 3.87479 3.02811 3 6.5 3C9.97189 3 10 3.87479 10 6.5C10 9.12521 10.0111 10 6.5 10C2.98893 10 3 9.12521 3 6.5Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M14 6.5C14 3.87479 14.0281 3 17.5 3C20.9719 3 21 3.87479 21 6.5C21 9.12521 21.0111 10 17.5 10C13.9889 10 14 9.12521 14 6.5Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M3 17.5C3 14.8748 3.02811 14 6.5 14C9.97189 14 10 14.8748 10 17.5C10 20.1252 10.0111 21 6.5 21C2.98893 21 3 20.1252 3 17.5Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M14 17.5C14 14.8748 14.0281 14 17.5 14C20.9719 14 21 14.8748 21 17.5C21 20.1252 21.0111 21 17.5 21C13.9889 21 14 20.1252 14 17.5Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Tender Info</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-tender" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['tender-create', 'tender-info'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('tender.*') ? 'active' : '' }}"
                                    href="{{ route('tender.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Tenders</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['tender-approval', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a href="{{ route('tlapproval') }}"
                                    class="nav-link d-flex align-items-center {{ request()->routeIs('tlapproval') || request()->routeIs('tlApprovalForm') ? 'active' : '' }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Approve Tender</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['phy-docs', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('phydocs.*') ? 'active' : '' }}"
                                    href="{{ route('phydocs.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Physical Docs</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['rfq', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('rfq.*') ? 'active' : '' }}"
                                    href="{{ route('rfq.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">RFQs</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['request-emd', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds.*') ? 'active' : '' }}"
                                    href="{{ route('emds.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">EMD/Tender Fees</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['tender-info', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a href="{{ route('checklist.index') }}"
                                    class="nav-link d-flex align-items-center {{ request()->routeIs('checklist.*') ? 'active' : '' }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Document Checklist</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['costing-sheet', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('googlesheet') ? 'active' : '' }}"
                                    href="{{ url('/admin/googlesheet') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Costing Sheet</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['costing-approval', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('costing-approval.*') ? 'active' : '' }}"
                                    href="{{ route('costing-approval.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal ">Costing Approval </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['bid-submission', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('bs.*') ? 'active' : '' }}"
                                    href="{{ route('bs.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal ">Bid Submission </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['tq-mgmt', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('tq_dashboard') ||
                                        request()->routeIs('tq_received_form') ||
                                        request()->routeIs('tq_received_form_post') ||
                                        request()->routeIs('tq_replied_form') ||
                                        request()->routeIs('tq_replied_form_post') ||
                                        request()->routeIs('tq_missed_form') ||
                                        request()->routeIs('tq_missed_form_post'),
                                ]) href="{{ url('/admin/tq_dashboard') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">TQ Managment </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['ra-mgmt', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('ra.*') ? 'active' : '' }}"
                                    href="{{ route('ra.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal ">RA Managment </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['results', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('results.*') ? 'active' : '' }}"
                                    href="{{ url('/tender/results') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Result</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['pqr-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('pqr_dashboard') ||
                                        request()->routeIs('pqr_dashboard_edit') ||
                                        request()->routeIs('pqr_dashboard_add'),
                                ]) href="{{ url('/admin/pqr_dashboard') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">PQR Dashboard</span>
                                </a>
                            <li
                                class="nav-item {{ array_intersect(['pqr-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('vendors.oem-files'),
                                ]) href="{{ route('vendors.oem-files') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">OEM Files</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebar-ops" role="button"
                            @class(['nav-link']) aria-expanded="false" aria-controls="sidebar-ops">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.9951 16.6766V14.1396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.19 5.33008C19.88 5.33008 21.24 6.70008 21.24 8.39008V11.8301C18.78 13.2701 15.53 14.1401 11.99 14.1401C8.45 14.1401 5.21 13.2701 2.75 11.8301V8.38008C2.75 6.69008 4.12 5.33008 5.81 5.33008H18.19Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M15.4951 5.32576V4.95976C15.4951 3.73976 14.5051 2.74976 13.2851 2.74976H10.7051C9.48512 2.74976 8.49512 3.73976 8.49512 4.95976V5.32576" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M2.77441 15.4829L2.96341 17.9919C3.09141 19.6829 4.50041 20.9899 6.19541 20.9899H17.7944C19.4894 20.9899 20.8984 19.6829 21.0264 17.9919L21.2154 15.4829" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Operations</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-ops" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['wo-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' =>
                                        request()->routeIs('basicdetailview') ||
                                        request()->routeIs('basicdetailadd') ||
                                        request()->routeIs('wodetailadd') ||
                                        request()->routeIs('woacceptanceform') ||
                                        request()->routeIs('woviewbuttenfoa'),
                                ]) href="{{ route('basicdetailview') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">WO Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['kickoff-meeting', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' =>
                                        request()->routeIs('kickmeeting_dashbord') ||
                                        request()->routeIs('initiate_meeting') ||
                                        request()->routeIs('viewbutten_dashboard'),
                                ]) href="{{ url('/admin/kickmeeting_dashbord') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Kick-off Meeting </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['contract-agreement', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' =>
                                        request()->routeIs('contract_dashboardview') ||
                                        request()->routeIs('viewbuttencontract'),
                                ]) href="{{ url('/admin/contract_dashboardview') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Contract Agreement </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebar-service" role="button"
                            @class(['nav-link']) aria-expanded="false" aria-controls="sidebar-service">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.9951 16.6766V14.1396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.19 5.33008C19.88 5.33008 21.24 6.70008 21.24 8.39008V11.8301C18.78 13.2701 15.53 14.1401 11.99 14.1401C8.45 14.1401 5.21 13.2701 2.75 11.8301V8.38008C2.75 6.69008 4.12 5.33008 5.81 5.33008H18.19Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M15.4951 5.32576V4.95976C15.4951 3.73976 14.5051 2.74976 13.2851 2.74976H10.7051C9.48512 2.74976 8.49512 3.73976 8.49512 4.95976V5.32576" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M2.77441 15.4829L2.96341 17.9919C3.09141 19.6829 4.50041 20.9899 6.19541 20.9899H17.7944C19.4894 20.9899 20.8984 19.6829 21.0264 17.9919L21.2154 15.4829" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Services</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-service" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['wo-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' => request()->routeIs('customer_service.*')])
                                    href="{{ route('customer_service.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Customer Service</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['wo-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' => request()->routeIs('customer_service.conference_call.index'),
                                ])
                                    href="{{ route('customer_service.conference_call.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Conference Call</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['wo-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link d-flex align-items-center',
                                    'active' => request()->routeIs('customer_service.service_visit.index'),
                                ])
                                    href="{{ route('customer_service.service_visit.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Service Visit</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'amc'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('amc.*') ? 'active' : '' }}"
                                    href="{{ route('amc.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">AMC</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('emds-dashboard.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#sidebar-bi" role="button"
                            aria-expanded="false" aria-controls="sidebar-bi">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.857 20.417C19.73 20.417 21.249 18.899 21.25 17.026V17.024V14.324C20.013 14.324 19.011 13.322 19.01 12.085C19.01 10.849 20.012 9.846 21.249 9.846H21.25V7.146C21.252 5.272 19.735 3.752 17.862 3.75H17.856H6.144C4.27 3.75 2.751 5.268 2.75 7.142V7.143V9.933C3.944 9.891 4.945 10.825 4.987 12.019C4.988 12.041 4.989 12.063 4.989 12.085C4.99 13.32 3.991 14.322 2.756 14.324H2.75V17.024C2.749 18.897 4.268 20.417 6.141 20.417H6.142H17.857Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12.3711 9.06303L12.9871 10.31C13.0471 10.432 13.1631 10.517 13.2981 10.537L14.6751 10.738C15.0161 10.788 15.1511 11.206 14.9051 11.445L13.9091 12.415C13.8111 12.51 13.7671 12.647 13.7891 12.782L14.0241 14.152C14.0821 14.491 13.7271 14.749 13.4231 14.589L12.1921 13.942C12.0711 13.878 11.9271 13.878 11.8061 13.942L10.5761 14.589C10.2711 14.749 9.91609 14.491 9.97409 14.152L10.2091 12.782C10.2321 12.647 10.1871 12.51 10.0891 12.415L9.09409 11.445C8.84809 11.206 8.98309 10.788 9.32309 10.738L10.7001 10.537C10.8351 10.517 10.9521 10.432 11.0121 10.31L11.6271 9.06303C11.7791 8.75503 12.2191 8.75503 12.3711 9.06303Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">BI Dashboards</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-bi" data-bs-parent="#sidebar">
                            <li class="nav-item d-none">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.*') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">EMD</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['bg-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.bg') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.bg') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Bank Guarantee</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['dd-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.dd') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.dd') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Demand Draft</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['bt-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.bt') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.bt') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Bank Transfer</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['pop-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.pop') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.pop') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Pay On Portal</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['chq-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.chq') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.chq') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Cheque</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['fdr-emds-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('emds-dashboard.fdr') ? 'active' : '' }}"
                                    href="{{ route('emds-dashboard.fdr') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">FDR</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['tender-fees', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('tender-fees.*') ? 'active' : '' }}"
                                    href="{{ route('tender-fees.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Tender Fees</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ array_intersect(['follow-up', 'all'], $permissions) ? '' : 'd-none' }}">
                        <a class="nav-link {{ request()->routeIs('followups.*') ? 'active' : '' }} "
                            aria-current="page" href="{{ route('followups.index') }}">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.8877 10.8967C19.2827 10.7007 20.3567 9.50473 20.3597 8.05573C20.3597 6.62773 19.3187 5.44373 17.9537 5.21973"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M19.7285 14.2505C21.0795 14.4525 22.0225 14.9255 22.0225 15.9005C22.0225 16.5715 21.5785 17.0075 20.8605 17.2815"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.8867 14.6638C8.67273 14.6638 5.92773 15.1508 5.92773 17.0958C5.92773 19.0398 8.65573 19.5408 11.8867 19.5408C15.1007 19.5408 17.8447 19.0588 17.8447 17.1128C17.8447 15.1668 15.1177 14.6638 11.8867 14.6638Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.8869 11.888C13.9959 11.888 15.7059 10.179 15.7059 8.069C15.7059 5.96 13.9959 4.25 11.8869 4.25C9.7779 4.25 8.0679 5.96 8.0679 8.069C8.0599 10.171 9.7569 11.881 11.8589 11.888H11.8869Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M5.88509 10.8967C4.48909 10.7007 3.41609 9.50473 3.41309 8.05573C3.41309 6.62773 4.45409 5.44373 5.81909 5.21973"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M4.044 14.2505C2.693 14.4525 1.75 14.9255 1.75 15.9005C1.75 16.5715 2.194 17.0075 2.912 17.2815"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Follow Ups</span>
                        </a>
                    </li>
                    <li class="nav-item {{ array_intersect(['courier', 'all'], $permissions) ? '' : 'd-none' }}">
                        <a class="nav-link {{ request()->routeIs('courier.*') ? 'active' : '' }} "
                            aria-current="page" href="{{ route('courier.index') }}">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.8325 8.17463L10.109 13.9592L3.59944 9.88767C2.66675 9.30414 2.86077 7.88744 3.91572 7.57893L19.3712 3.05277C20.3373 2.76963 21.2326 3.67283 20.9456 4.642L16.3731 20.0868C16.0598 21.1432 14.6512 21.332 14.0732 20.3953L10.106 13.9602"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Couriers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebar-account" role="button"
                            @class(['nav-link']) aria-expanded="false" aria-controls="sidebar-account">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21.6389 14.3957H17.5906C16.1042 14.3948 14.8993 13.1909 14.8984 11.7045C14.8984 10.218 16.1042 9.01409 17.5906 9.01318H21.6389"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M18.049 11.6429H17.7373" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.74766 3H16.3911C19.2892 3 21.6388 5.34951 21.6388 8.24766V15.4247C21.6388 18.3229 19.2892 20.6724 16.3911 20.6724H7.74766C4.84951 20.6724 2.5 18.3229 2.5 15.4247V8.24766C2.5 5.34951 4.84951 3 7.74766 3Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M7.03516 7.5382H12.4341" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Accounts</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-account" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['employee-imprest', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link {{ request()->url() == url('/admin/employeeimprest') ? 'active' : '' }}
                                    {{ request()->url() == url('/admin/employeeimprest_add') ? 'active' : '' }}
                                    {{ request()->url() == url('/admin/employeeimprest_account') ? 'active' : '' }}"
                                    aria-current="page"
                                    href="{{ Str::startsWith(Auth::user()->role, 'account') || Auth::user()->role == 'admin' ? url('/admin/employeeimprest_account') : url('/admin/employeeimprest') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Employee Imprest</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['finance-docs', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('finance') ||
                                        request()->routeIs('finance_add') ||
                                        request()->routeIs('finance_edit'),
                                ]) href="{{ url('/admin/finance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Finance Docs</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['loan-advances', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('loanadvances') ||
                                        request()->routeIs('loanadvancesadd') ||
                                        request()->routeIs('loanadvancescreate') ||
                                        request()->routeIs('loanadvancesdelete') ||
                                        request()->routeIs('loanadvancesupdate') ||
                                        request()->routeIs('loanadvancesedit') ||
                                        request()->routeIs('dueview') ||
                                        request()->routeIs('dueemiadd') ||
                                        request()->routeIs('dueemiupdate') ||
                                        request()->routeIs('dueemiupdatepost') ||
                                        request()->routeIs('dueemidelete') ||
                                        request()->routeIs('loancloseupdate') ||
                                        request()->routeIs('loancloseupdate_post') ||
                                        request()->routeIs('tdsrecoveryview') ||
                                        request()->routeIs('tdsrecoveryadd') ||
                                        request()->routeIs('tdsrecoveryupdate') ||
                                        request()->routeIs('tdsrecoveryupdatepost') ||
                                        request()->routeIs('tdsrecoverydelete'),
                                ]) href="{{ url('/admin/loanadvances') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Loan & Advance</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['projects', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('projects.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/projects') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Projects</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'account-checklist'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('checklists.*') ? 'active' : '' }}"
                                    href="{{ url('/accounts/checklists') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Account Checklist</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'expense-checklist'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('fixed-expenses.index') ? 'active' : '' }}"
                                    href="{{ url('/accounts/fixed-expenses') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Expense Checklist</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'gstr1-checklist'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('gstr1.index') ? 'active' : '' }}"
                                    href="{{ url('/accounts/gstr1') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">GST R1 Checklist</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'gst3b-checklist'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('gst3b.index') ? 'active' : '' }}"
                                    href="{{ url('/accounts/gst3b') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">GST 3B Checklist</span>
                                </a>
                            </li>
                            <li class="nav-item {{ array_intersect(['all', 'tds-checklist'], $permissions) ? '' : 'd-none' }}">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('tds.index') ? 'active' : '' }}"
                                    href="{{ url('/accounts/tds') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">TDS Checklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#sidebar-docDash" role="button"
                            @class(['nav-link']) aria-expanded="false" aria-controls="sidebar-docDash">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.419 15.732C21.419 19.31 19.31 21.419 15.732 21.419H7.95C4.363 21.419 2.25 19.31 2.25 15.732V7.932C2.25 4.359 3.564 2.25 7.143 2.25H9.143C9.861 2.251 10.537 2.588 10.967 3.163L11.88 4.377C12.312 4.951 12.988 5.289 13.706 5.29H16.536C20.123 5.29 21.447 7.116 21.447 10.767L21.419 15.732Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M7.48145 14.4629H16.2164" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Docs Dashboard</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-docDash" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['client-directory', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('clientdirectory') ||
                                        request()->routeIs('clientdirectoryadd') ||
                                        request()->routeIs('clientdirectoryedit'),
                                ]) href="{{ url('/admin/clientdirectory') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Client Directory</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['rent-agreement', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('rent') ||
                                        request()->routeIs('rent_add') ||
                                        request()->routeIs('rent_edit'),
                                ]) href="{{ url('/admin/rent') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Rent Agreement</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ array_intersect(['admin', 'all'], $permissions) ? '' : 'd-none' }}">
                        <a class="nav-link {{ request()->routeIs('admin/user/*') ? 'active' : '' }} "
                            data-bs-toggle="collapse" href="#sidebar-user" role="button" aria-expanded="false"
                            aria-controls="sidebar-user">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.9846 21.606C11.9846 21.606 19.6566 19.283 19.6566 12.879C19.6566 6.474 19.9346 5.974 19.3196 5.358C18.7036 4.742 12.9906 2.75 11.9846 2.75C10.9786 2.75 5.26557 4.742 4.65057 5.358C4.03457 5.974 4.31257 6.474 4.31257 12.879C4.31257 19.283 11.9846 21.606 11.9846 21.606Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path d="M9.38574 11.8746L11.2777 13.7696L15.1757 9.86963" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Admin</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-user" data-bs-parent="#sidebar">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin/user/*') ? 'active' : '' }}"
                                    href="{{ url('/admin/user/all') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Employees</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('organizations.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/organizations') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Organizations</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('statuses.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/statuses') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Statuses</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('submitteddocs.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/submitteddocs') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Document Submitted</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('items.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/items') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Items</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('vendors.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/vendors') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Vendors</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('websites.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/websites') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Websites</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center {{ request()->routeIs('locations.index') ? 'active' : '' }}"
                                    href="{{ url('/admin/locations') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Locations</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('categories') ||
                                        request()->routeIs('categories_add') ||
                                        request()->routeIs('category_edit'),
                                ]) href="{{ url('/admin/categories') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Imprest Category</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('documenttype') ||
                                        request()->routeIs('documenttype_add') ||
                                        request()->routeIs('documenttype_edit'),
                                ]) href="{{ url('/admin/documenttype') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Document Type</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' =>
                                        request()->routeIs('financialyear') ||
                                        request()->routeIs('financialyear_add') ||
                                        request()->routeIs('financialyear_edit'),
                                ]) href="{{ url('/admin/financialyear') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Financial Year</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('followupcategories'),
                                ])
                                    href="{{ url('admin/followups/followup-categories') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Followup categories</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('emd-responsibility.*'),
                                ])
                                    href="{{ route('emd-responsibility.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">EMD Responsibilities</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="nav-item {{ array_intersect(['te-dashboard', 'tl-dashboard', 'operation-dashboard', 'account-dashboard', 'oem-dashboard', 'business-dashboard', 'customer-dashboard', 'location-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-performance" role="button"
                            aria-expanded="false" aria-controls="sidebar-performance">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M13.1043 4.17701L14.9317 7.82776C15.1108 8.18616 15.4565 8.43467 15.8573 8.49218L19.9453 9.08062C20.9554 9.22644 21.3573 10.4505 20.6263 11.1519L17.6702 13.9924C17.3797 14.2718 17.2474 14.6733 17.3162 15.0676L18.0138 19.0778C18.1856 20.0698 17.1298 20.8267 16.227 20.3574L12.5732 18.4627C12.215 18.2768 11.786 18.2768 11.4268 18.4627L7.773 20.3574C6.87023 20.8267 5.81439 20.0698 5.98724 19.0778L6.68385 15.0676C6.75257 14.6733 6.62033 14.2718 6.32982 13.9924L3.37368 11.1519C2.64272 10.4505 3.04464 9.22644 4.05466 9.08062L8.14265 8.49218C8.54354 8.43467 8.89028 8.18616 9.06937 7.82776L10.8957 4.17701C11.3477 3.27433 12.6523 3.27433 13.1043 4.17701Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">Performance</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-performance" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['te-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('employee/performance'),
                                ]) href="{{ route('employee/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">TE Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['tl-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('team-leader/performance'),
                                ]) href="{{ route('team-leader/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">TL Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['operation-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('operation/performance'),
                                ]) href="{{ route('operation/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Operation Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['account-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('accounts/performance'),
                                ]) href="{{ route('accounts/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Account Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['oem-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('oem/performance'),
                                ]) href="{{ route('oem/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">OEM Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['business-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('business/performance'),
                                ]) href="{{ route('business/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Business Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['customer-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('customer/performance'),
                                ]) href="{{ route('customer/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Customer Dashboard</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['location-dashboard', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('location/performance'),
                                ]) href="{{ route('location/performance') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Location Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ array_intersect(['leads', 'all'], $permissions) ? '' : 'd-none' }}">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-crm" role="button"
                            aria-expanded="false" aria-controls="sidebar-crm">
                            <i class="icon">
                                <svg width="22" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.37121 10.2017V17.0618" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M12.0382 6.91919V17.0619" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M16.6285 13.8269V17.0619" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M16.6857 2H7.31429C4.04762 2 2 4.31208 2 7.58516V16.4148C2 19.6879 4.0381 22 7.31429 22H16.6857C19.9619 22 22 19.6879 22 16.4148V7.58516C22 4.31208 19.9619 2 16.6857 2Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </i>
                            <span class="item-name">CRM</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse" id="sidebar-crm" data-bs-parent="#sidebar">
                            <li
                                class="nav-item {{ array_intersect(['leads', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('lead.*'),
                                ]) href="{{ route('lead.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Leads</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['enquiry', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('enquiries.*'),
                                ]) href="{{ route('enquiries.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Enquiries</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['approvalPvtCosting', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('private-costing-sheet.*'),
                                ])
                                    href="{{ route('private-costing-sheet.approval') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Costing Approval</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ array_intersect(['pvt-quotes', 'all'], $permissions) ? '' : 'd-none' }}">
                                <a @class([
                                    'nav-link',
                                    'd-flex',
                                    'align-items-center',
                                    'active' => request()->routeIs('pvt-quotes.*'),
                                ]) href="{{ route('pvt-quotes.index') }}">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="item-name fw-normal">Quotations</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    <main class="main-content">
        <div class="position-relative">
            <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar border-bottom">
                <div class="container-fluid navbar-inner">
                    <a href="" class="navbar-brand"></a>
                    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                        <i class="icon">
                            <svg width="20px" height="20px" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                            </svg>
                        </i>
                    </div>
                    <h4 class="title ">
                        @yield('page-title')
                    </h4>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <span class="navbar-toggler-bar bar1 mt-2"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto navbar-list mb-2 mb-lg-0 align-items-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if (Auth::user()->image)
                                        <img src="{{ asset('uploads/' . Auth::user()->image) }}" alt="User-Profile"
                                            class="img-fluid avatar avatar-50 avatar-rounded">
                                    @else
                                        <img src="{{ asset('assets/images/avatars/01.png') }}" alt="User-Profile"
                                            class="img-fluid avatar avatar-50 avatar-rounded">
                                    @endif
                                    <div class="caption ms-3 ">
                                        <h6 class="mb-0 caption-title">{{ Auth::user()->name }}</h6>
                                        <p class="mb-0 caption-sub-title">{{ ucfirst(Auth::user()->role) }}</p>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li class="border-0">
                                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"
                                            onclick="toggleThemeMode(this)">
                                            <i class="fa fa-sun me-2"></i>
                                            <span>Theme</span>
                                        </a>
                                    </li>
                                    <li class="border-0">
                                        <a class="dropdown-item"
                                            href="{{ route('profile.edit', Auth::user()->id) }}">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="border-0">
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> <!--Nav End-->
        </div>
        <div class="container-fluid content-inner pb-0">
            @yield('content')
        </div>
    </main>

    <div class="d-none">
        <?php

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        // echo finfo_file($finfo, 'uploads/docs/GeM-Bidding-7771730.pdf_1745655322_1368714793.pdf');

        $required_extensions = ['fileinfo', 'gd', 'mbstring', 'xml', 'zip', 'dom', 'curl', 'json', 'intl'];

        echo '<h5>PHP Extension Check</h5>';
        foreach ($required_extensions as $ext) {
            echo $ext . ': ' . (extension_loaded($ext) ? '✅ Enabled' : '❌ Disabled') . '<br>';
        }
        ?>
    </div>

    <footer class="footer">
        <div class="footer-body">
            <div class="text-center">
            </div>
            <div class="right-panel">
                &copy;
                <script>
                    document.write(new Date().getFullYear())
                </script> TMS, All rights reserved. |

                <span class="connection-icon me-2"></span>
                <span class="connection-text"></span>
                <span class="connection-speed"></span>
            </div>
        </div>
    </footer>


    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>
    <script src="{{ asset('assets/js/fslightbox.js') }}"></script>
    <script src="{{ asset('assets/js/prism.mini.js') }}"></script>
    <script src="{{ asset('assets/js/charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/filepond.js') }}"></script>
    <script src="{{ asset('assets/js/filepond.jquery.js') }}"></script>
    <script src="{{ asset('assets/js/file-pond-img-preview.js') }}"></script>
    <script src="{{ asset('assets/js/main.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="{{ asset('assets/vendor/fontawesome-6.6.0/js/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/fontawesome-6.6.0/js/brands.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/fontawesome-6.6.0/js/regular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/fontawesome-6.6.0/js/solid.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2-4.1.0/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/44.0.0/ckeditor5.umd.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <!--<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>-->
    <!--<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>-->
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="{{ asset('assets/js/ckeditor.js') }}"></script>

    @stack('scripts')
    <script>
        $(document).ready(function() {
            // which form field has required attribute add * on it's label
            $('input[required], select[required], textarea[required]').each(function() {
                var label = $(this).prev('label');
                if (label.length) {
                    label.append('<span class="text-danger">*</span>');
                }
            });

            // auto height for textarea
            $('textarea').each(function() {
                $(this).css('height', $(this).prop('scrollHeight') + 'px');
            });

            function updateConnectionStatus() {
                const connectionIcon = document.querySelector('.connection-icon');
                const connectionText = document.querySelector('.connection-text');
                const connectionSpeed = document.querySelector('.connection-speed');

                if (!navigator.onLine) {
                    connectionIcon.className = 'connection-icon offline';
                    connectionText.textContent = 'No Internet';
                    connectionSpeed.textContent = '';
                    return;
                }

                // Test connection speed
                const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
                if (connection) {
                    // Using Network Information API if available
                    const speed = connection.downlink; // Speed in Mbps
                    connectionSpeed.textContent = `(${speed} Mbps)`;

                    switch (connection.effectiveType) {
                        case 'slow-2g':
                        case '2g':
                            connectionIcon.className = 'connection-icon slow';
                            connectionText.textContent = 'Slow';
                            break;
                        case '3g':
                            connectionIcon.className = 'connection-icon slow';
                            connectionText.textContent = 'Fair';
                            break;
                        case '4g':
                            connectionIcon.className = 'connection-icon fair';
                            connectionText.textContent = 'Good';
                            break;
                        default:
                            connectionIcon.className = 'connection-icon fair';
                            connectionText.textContent = 'Good';
                    }
                } else {
                    // Fallback speed test using image loading
                    const startTime = performance.now();
                    const img = new Image();
                    img.onload = function() {
                        const endTime = performance.now();
                        const loadTime = endTime - startTime;
                        const speedMbps = (0.001 / loadTime) * 1000; // Rough estimation of speed in Mbps

                        connectionSpeed.textContent = `${speedMbps.toFixed(1)} Mbps`;

                        if (loadTime > 1000) {
                            connectionIcon.className = 'connection-icon slow';
                            connectionText.textContent = 'Slow';
                        } else if (loadTime > 300) {
                            connectionIcon.className = 'connection-icon slow';
                            connectionText.textContent = 'Fair';
                        } else {
                            connectionIcon.className = 'connection-icon fair';
                            connectionText.textContent = 'Good';
                        }
                    };
                    img.src =
                        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFhAJ/wlseKgAAAABJRU5ErkJggg==';
                }
            }
            updateConnectionStatus();
            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);
            setInterval(updateConnectionStatus, 5000);
        });
    </script>
    <script>
        function applyTheme(themeName, el = null) {
            const themeLink = document.getElementById('theme-style');
            // themeLink.setAttribute('href', `assets/css/${themeName}`);
            themeLink.setAttribute('href', ASSET_BASE + themeName);

            localStorage.setItem('theme', themeName);
            if (el) {
                const icon = el.querySelector('svg');
                if (icon) {
                    if (themeName === 'coinex.css') {
                        icon.setAttribute('data-icon', 'moon');
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    } else {
                        icon.setAttribute('data-icon', 'sun');
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    }
                }
            }
        }

        function toggleThemeMode(el) {
            const themeLink = document.getElementById('theme-style');
            const currentTheme = themeLink.getAttribute('href');
            const currentFile = currentTheme.split('/').pop();
            const isLight = currentFile === 'coinex.css';

            const newTheme = isLight ? 'old_coinex.css' : 'coinex.css';
            applyTheme(newTheme, el);
        }

        document.addEventListener('DOMContentLoaded', function() {
            let savedTheme = localStorage.getItem('theme');

            if (!savedTheme) {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                savedTheme = prefersDark ? 'old_coinex.css' : 'coinex.css';
            }

            applyTheme(savedTheme);
        });
    </script>

</body>

</html>
