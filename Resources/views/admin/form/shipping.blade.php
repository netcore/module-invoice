<h3 id="shipping">Shipping</h3>

<div class="panel panel-default">
    <div class="panel-body">
        @php
            $shippingOptions = \Modules\Product\Models\ShippingOption::with('translations', 'locations')->get();
            $shippingOptionsList = $shippingOptions->pluck('name', 'id');
            $handler = app($model->service->shippingOption->handler);
        @endphp

        @if($success = session('shippingSuccess'))
            <div class="alert alert-success">{{ $success }}</div>
        @endif

        @if($error = session('shippingError'))
            <div class="alert alert-danger">{{ $error }}</div>
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

        @if($model->service->shippingOption)
            {{-- Pickup location select --}}
            @if($model->service->shippingOption->hasSelectableLocations())
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

            {{-- Service type --}}
            @if(method_exists($handler, 'getServicesOfType'))
                @php
                    $serviceTypes = $handler->getServicesOfType($model->service->shippingOption->type);
                @endphp

                <div class="form-group">
                    {{ Form::label('service_type', 'Service type:') }}
                    {{ Form::select('service_type', $serviceTypes, $model->service->service_type, [
                        'class' => 'form-control'
                    ]) }}
                </div>
            @endif
        @endif

        <div class="form-group">
            {{ Form::label('weight', 'Weight (kg):') }}
            {{ Form::number('serviceFields[weight]', $model->service->getField('weight'), [
                'class' => 'form-control',
                'id'    => 'weight',
                'step'  => 0.1,
                'min'   => 0
            ]) }}
        </div>

        @if(!$model->service->is_sent_to_service)
            <button type="submit" class="btn btn-warning" name="submitToService">
                <i class="fa fa-paper-plane-o"></i> Send to service
            </button>
        @else
            <button type="submit" class="btn btn-danger" name="deleteFromService">
                <i class="fa fa-trash"></i>
                Delete parcel {{ $handler->canBeDeletedUsingService() ? 'from service' : 'locally' }}
            </button>

            @if(method_exists($handler, 'getParcelLabel'))
                <a href="{{ route('invoice::print-label', $model) }}" class="btn btn-warning" target="_blank">
                    <i class="fa fa-print"></i> Print parcel label
                </a>
            @endif
        @endif
    </div>
</div>