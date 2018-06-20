<h3 id="shipping">Shipping</h3>

<div class="panel panel-default">
    <div class="panel-body">
        @php
            $shippingOptions = \Modules\Product\Models\ShippingOption::with('translations', 'locations')->get();
            $shippingOptionsList = $shippingOptions->pluck('name', 'id');
        @endphp

        @if($success = session('shippingSuccess'))
            <div class="alert alert-success">{{ $success }}</div>
        @endif

        <div class="form-group">
            {{ Form::label('shipping_option_id', 'Shipping option:') }}
            <div class="input-group">
                {{ Form::select('shipping_option_id', $shippingOptionsList, $model->service->shipping_option_id, [
                    'class' => 'form-control'
                ]) }}

                <div class="input-group-btn">
                    <button type="submit" class="btn btn-success" name="setShipping">
                        <i class="fa fa-check"></i> Set option
                    </button>
                </div>
            </div>
        </div>

        @if($model->service->shippingOption && $model->service->shippingOption->hasSelectableLocations())
            @php
                $locations = [];

                if ($model->service->shippingOption) {
                    $locations = $model->service->shippingOption->locations->pluck('location', 'id');
                }
            @endphp

            <div class="form-group">
                {{ Form::label('shipping_option_location_id', 'Shipping option location:') }}
                {{ Form::select('shipping_option_location_id', $locations, $model->service->shipping_option_location_id, [
                    'class' => 'form-control'
                ]) }}
            </div>
        @endif

        <button type="submit" class="btn btn-warning" name="submitToService">
            <i class="fa fa-airplane-o"></i> Send to service
        </button>
    </div>

    <div class="panel-footer text-right">
        <button class="btn btn-info">
            <i class="fa fa-plus"></i> Add shipping to invoice
        </button>
    </div>
</div>