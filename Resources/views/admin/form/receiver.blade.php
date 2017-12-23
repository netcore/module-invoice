<h3>Receiver</h3>

@php
    $receiverDefaultFields = config('netcore.module-invoice.create_default_fields.receiver');
    $receiverKeyValuePairs = $receiverDefaultFields;
    if($model->exists) {
        $receiverKeyValuePairs = $model->receiver_data;
    }
@endphp

@foreach($receiverKeyValuePairs as $key => $value)
    <fieldset class="form-group form-group-lg {{ $errors->has('receiver_data['.$key.']') ? 'form-message-light has-error has-validation-error' : '' }}">
        <label>
            {{ title_case(str_replace('_', ' ', $key)) }}
        </label>

        {{ Form::text('receiver_data['.$key.']', $value, [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]) }}

        @if ($errors->has('receiver_data.'.$key))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get('receiver_data['.$key.']') as $message)
                    {{ $message }} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach
