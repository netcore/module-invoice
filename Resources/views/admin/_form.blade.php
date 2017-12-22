
<div id="form-loading">
    <span class="fa fa-gear fa-spin"></span> Loading...
</div>

<div class="p-x-1" id="form-body" hidden>

    @include('invoice::admin.form.relations')
    @include('invoice::admin.form.base')

    <div class="row">
        <div class="col-md-6">
            @include('invoice::admin.form.sender')
        </div>
        <div class="col-md-6">
            @include('invoice::admin.form.receiver')
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('invoice::admin.form.items')
            @include('invoice::admin.form.totals')
        </div>
    </div>

    <button type="submit" class="btn btn-md btn-success m-t-3 pull-xs-right">
        <i class="fa fa-save"></i> Save
    </button>

    <a href="{{ route('invoice::index') }}" class="btn btn-md btn-default m-t-3 m-r-1 pull-xs-right">
        <i class="fa fa-undo"></i> Back
    </a>
</div>

@section('scripts')
    @include('invoice::admin.form._scripts')
@endsection

@section('styles')
    @include('invoice::admin.form._styles')
@endsection
