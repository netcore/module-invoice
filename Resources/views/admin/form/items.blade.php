

@php
$languages = \Netcore\Translator\Helpers\TransHelper::getAllLanguages();
@endphp

<h3>Items</h3>

<table id="invoice-items-table">
    <tr>
        <th>Name</th>
        <th>Price with VAT</th>
        <th>Price without VAT</th>
        <th>Quantity</th>
    </tr>

    @foreach($model->items as $item)
        <tr>

            <td>
                @if($languages->count() > 1)
                    <table>
                        @foreach($languages as $language)
                            <tr>
                                <td>
                                    {{ strtoupper($language->iso_code) }}:
                                </td>
                                <td>
                                    {{ Form::text('items['.$item->id.']['.$language->iso_code.'][name]', trans_model($item, $language, 'name'), [
                                        'class' => 'form-control',
                                        'autocomplete' => 'off'
                                    ]) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    @foreach($languages as $language)
                        {{ Form::text('items['.$item->id.']['.$language->iso_code.'][name]', trans_model($item, $language, 'name'), [
                            'class' => 'form-control',
                            'autocomplete' => 'off'
                        ]) }}
                    @endforeach
                @endif
            </td>

            <td>
                {{ Form::number('items['.$item->id.'][price_with_vat]', $item->price_with_vat, [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]) }}
            </td>

            <td>
                {{ Form::number('items['.$item->id.'][price_without_vat]', $item->price_without_vat, [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]) }}
            </td>

            <td>
                {{ Form::number('items['.$item->id.'][quantity]', $item->quantity, [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]) }}
            </td>


            @php
            /*
            katram itemam ir:
            price_with_vat
            price_without_vat
            quantity
            name(tulkojams)
            */
            @endphp

            {{--
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
            --}}
        </tr>
    @endforeach
</table>

