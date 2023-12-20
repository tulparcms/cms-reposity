@extends('tcms.admin.default.layout-dash')
@section('pageTitle')
{{ __(REPOSITY_LANG.'Reposity') }}
@endsection
@section('navbarItem')
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow btn-popup-form" href="javascript:void(0);"  data-url="{{ route('admin.reposity.add-reposity') }}">
            <i class="ti ti-layout-grid-add ti-md"></i>
        </a>
    </li>
@endsection
@section('head')
    {!! TCMS()->dataTableCss() !!}
@endsection
@section('content')
        <div class="card"><x-admin.default.datatable :dataTable="$dataTable" /></div>
@endsection
@section('footer')
    {!! TCMS()->dataTableJs() !!}
@endsection
