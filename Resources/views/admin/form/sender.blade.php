

<h3>Sender</h3>

@php
    $senderDefaultFields = config('netcore.module-invoice.create_default_fields.sender');
    $senderKeyValuePairs = $senderDefaultFields;
    if(isset($model)) {
        $senderKeyValuePairs = $model->sender_data;
    }
@endphp
@foreach($senderKeyValuePairs as $key => $value)
    <fieldset class="form-group form-group-lg {{ $errors->has('sender_data['.$key.']') ? 'form-message-light has-error has-validation-error' : '' }}">
        <label>
            {{ title_case(str_replace('_', ' ', $key)) }}
        </label>

        {{ Form::text('sender_data['.$key.']', $value, [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]) }}

        @if ($errors->has('sender_data.'.$key))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get('sender_data['.$key.']') as $message)
                    {{ $message }} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach

{{--
@foreach($fields as $field => $type)
@endforeach

<fieldset class="form-group form-group-lg {{ $errors->has('invoice_nr') ? 'form-message-light has-error has-validation-error' : '' }}">
    <label for="invoice_nr">Invoice Nr.</label>

    {{ Form::text('invoice_nr', null, [
        'id' => 'invoice_nr',
        'class' => 'form-control',
        'autocomplete' => 'off'
    ]) }}

    @if ($errors->has('invoice_nr'))
        <div id="validation-message-light-error" class="form-message validation-error">
            @foreach ($errors->get('invoice_nr') as $message)
                {{ $message }} <br>
            @endforeach
        </div>
    @endif
</fieldset>
--}}
