@extends('admin::layouts.master')

@section('content')

    {!! Breadcrumbs::render('admin.invoice') !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            Invoices &nbsp; <span class="label label-info">{{ invoice()->totalCount() }}</span>
            <div class="pull-right">
                <a href="{{ route('invoice::create') }}" class="btn btn-xs btn-success">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </div>
        <div class="panel-body overflow-x-auto">
            <div class="table-primary">
                <table
                        class="table table-bordered table-stripped"
                        id="invoices-datatable"
                        data-caption="Invoices"
                        data-ajax="{{ route('invoice::datatable-pagination') }}"
                >
                    <thead>
                        <tr>
                            <th>Invoice nr.</th>
                            <th>Date</th>
                            @foreach($relations as $relation)
                                <th>{{ $relation['table']['name'] }}</th>
                            @endforeach
                            <th>Total without VAT</th>
                            <th>Total with VAT</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style type="text/css">
        .overflow-x-auto { overflow-x: auto; }
    </style>
@endsection

@section('scripts')
    <script type="text/javascript">
        var enabledRelations = {!! json_encode($relations) !!};
    </script>
    <script src="{{ versionedAsset('assets/invoice/admin/js/index.js') }}"></script>
@endsection
