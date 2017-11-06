@extends('admin::layouts.master')

@section('content')

    <ol class="breadcrumb page-breadcrumb">
        <li><a href="{{ route('admin::dashboard.index') }}">Admin</a></li>
        <li class="active">Invoices</li>
    </ol>

    <div class="panel panel-default">
        <div class="panel-heading">Invoices &nbsp; <span class="label label-info">0</span></div>
        <div class="panel-body overflow-x-auto">
            <table
                    class="table table-bordered table-stripped"
                    id="invoices-datatable"
                    data-caption="Invoices"
                    data-ajax="/"
            >
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>User</th>
                    <th>Payment method</th>
                    <th>Total sum</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@section('styles')
    <style type="text/css">
        .overflow-x-auto { overflow-x: auto; }
    </style>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/invoice/admin/js/index.js') }}"></script>
@endsection
