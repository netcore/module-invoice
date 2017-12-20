
@php
    $fields = $model->getFields();
    $fields = [
        //"currency_id" => "text",
        //"user_id" => "text",
        "invoice_nr" => "text",
        "total_with_vat" => "text",
        "total_without_vat" => "text",
        "vat" => "text",
        //"payment_details" => "text",
        //"sender_data" => "textarea",
        //"receiver_data" => "textarea",
        //"data" => "textarea"
    ];
@endphp

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

<fieldset class="form-group form-group-lg {{ $errors->has('total_with_vat') ? 'form-message-light has-error has-validation-error' : '' }}">
    <label for="total_with_vat">Total with VAT</label>

    {{ Form::number('total_with_vat', null, [
        'id' => 'total_with_vat',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'min' => '0',
        'max' => '10000',
        'step' => '0.01'
    ]) }}

    @if ($errors->has('total_with_vat'))
        <div id="validation-message-light-error" class="form-message validation-error">
            @foreach ($errors->get('total_with_vat') as $message)
                {{ $message }} <br>
            @endforeach
        </div>
    @endif
</fieldset>

<fieldset class="form-group form-group-lg {{ $errors->has('total_without_vat') ? 'form-message-light has-error has-validation-error' : '' }}">
    <label for="total_without_vat">Total without VAT</label>

    {{ Form::number('total_without_vat', null, [
        'id' => 'total_without_vat',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'min' => '0',
        'max' => '10000',
        'step' => '0.01'
    ]) }}

    @if ($errors->has('total_without_vat'))
        <div id="validation-message-light-error" class="form-message validation-error">
            @foreach ($errors->get('total_without_vat') as $message)
                {{ $message }} <br>
            @endforeach
        </div>
    @endif
</fieldset>

<fieldset class="form-group form-group-lg {{ $errors->has('vat') ? 'form-message-light has-error has-validation-error' : '' }}">
    <label for="vat">VAT</label>

    {{ Form::number('vat', null, [
        'id' => 'vat',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'min' => '0',
        'max' => '100',
        'step' => '0.01'
    ]) }}

    @if ($errors->has('vat'))
        <div id="validation-message-light-error" class="form-message validation-error">
            @foreach ($errors->get('vat') as $message)
                {{ $message }} <br>
            @endforeach
        </div>
    @endif
</fieldset>
