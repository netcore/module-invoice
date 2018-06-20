<div id="form-loading">
    <span class="fa fa-gear fa-spin"></span> Loading...
</div>

<div class="p-x-1" id="form-body" hidden>
    <div class="form-group">
        {!! Form::label('type', 'Type') !!}
        {!! Form::select('type', [
            'invoice' => 'Invoice',
            'refund'  => 'Refund'
        ], null, [
            'class' => 'form-control input-lg'
        ]) !!}
    </div>

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
        <div class="col-xs-12">
            @include('invoice::admin.form.shipping')
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

    @if(isset($model) && $model->exists && $model->status !== 'new')
        <a href="{{ route('invoice::send', $model) }}"
           class="btn btn-md btn-{{ $model->is_sent ? 'danger' : 'success' }} m-t-3 m-r-1 pull-xs-right">
            <i class="fa fa-send"></i> {{ $model->is_sent ? 'Resend' : 'Send' }} invoice to client
        </a>
    @endif
</div>

@section('scripts')
    @include('invoice::admin.form._scripts')
@endsection

@section('styles')
    @include('invoice::admin.form._styles')
@endsection
