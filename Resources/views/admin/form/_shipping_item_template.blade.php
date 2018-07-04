@php
    $itemId = '{{ itemId }}';

    $names = [
        'en' => 'Delivery',
        'lv' => 'Piegāde',
        'ru' => 'Доставка'
    ];
@endphp

<tr id="invoice-item-{{ $itemId }}" class="invoice-item-tr">

    <td>
        @if($languages->count() > 1)
            <table>
                @foreach($languages as $language)
                    @php
                        $name = $names[$language->iso_code] ?? '';
                    @endphp

                    <tr>
                        <td>
                            {{ strtoupper($language->iso_code) }}:
                        </td>
                        <td>
                            {{ Form::text('items['.$itemId.'][translations]['.$language->iso_code.'][name]', $name, [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ]) }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            @foreach($languages as $language)
                @php
                    $name = $names[$language->iso_code] ?? '';
                @endphp

                {{ Form::text('items['.$itemId.'][translations]['.$language->iso_code.'][name]', trans_model($item, $language, 'name'), [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]) }}
            @endforeach
        @endif
    </td>

    @php
        $configuredInvoiceItemVariables = config('netcore.module-invoice.invoice_item_variables', []);
    @endphp

    @foreach($configuredInvoiceItemVariables as $configuredVariable)
        @php
            $variableObject = isset($item) ? $item->variables->where('key', $configuredVariable)->first() : null;
            $variableValue = $variableObject ? $variableObject->value : '';
        @endphp
        <td>
            {{ Form::text('items['.$itemId.'][variables]['.$configuredVariable.']', $variableValue, [
                'class' => 'form-control',
                'autocomplete' => 'off'
            ]) }}
        </td>
    @endforeach

    <td>
        {{ Form::number('items['.$itemId.'][price_without_vat]', 0, [
            'class' => 'form-control calculations-price-without-vat calculations-input',
            'autocomplete' => 'off',
            'min' => '0',
            'max' => '9999',
            'step' => '0.01'
        ]) }}
    </td>

    <td>
        {{ Form::number('items['.$itemId.'][price_with_vat]', 0, [
            'class' => 'form-control calculations-price-with-vat calculations-input',
            'autocomplete' => 'off',
            'min' => '0',
            'max' => '9999',
            'step' => '0.01'
        ]) }}
    </td>

    <td>
        {{ Form::number('items['.$itemId.'][quantity]', 1, [
            'class' => 'form-control calculations-quantity',
            'autocomplete' => 'off',
            'min' => '0',
            'max' => '9999',
            'step' => '1'
        ]) }}
    </td>

    <td>
        <a
                class="btn btn-xs btn-danger delete-invoice-item full-width max-width-100"
                data-title="Confirmation"
                data-text="Item will be deleted. Are you sure?"
                data-confirm-button-text="Delete"
                data-method="DELETE"
                data-success-title="Success"
                data-success-text="Items was deleted"
                data-fade-out-selector="#invoice-item-{{ $itemId }}"
        >
            Delete
        </a>
    </td>
</tr>