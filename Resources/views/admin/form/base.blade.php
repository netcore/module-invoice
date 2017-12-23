
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

