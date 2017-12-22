@php
    $itemId = isset($item) ? $item->id : '{{ itemId }}';
    $priceWithVat = isset($item) ? $item->price_with_vat : '';
    $priceWithoutVat = isset($item) ? $item->price_without_vat : '';
    $quantity = isset($item) ? $item->quantity : '';
@endphp

<tr id="invoice-item-{{ $itemId }}" class="invoice-item-tr">

    <td>
        @if($languages->count() > 1)
            <table>
                @foreach($languages as $language)
                    @php
                        $name = isset($item) ? trans_model($item, $language, 'name') : '';
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
                    $name = isset($item) ? trans_model($item, $language, 'name') : '';
                @endphp
                {{ Form::text('items['.$itemId.'][translations]['.$language->iso_code.'][name]', trans_model($item, $language, 'name'), [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]) }}
            @endforeach
        @endif
    </td>

    <td>
        {{ Form::number('items['.$itemId.'][price_without_vat]', $priceWithoutVat, [
            'class' => 'form-control calculations-price-without-vat',
            'autocomplete' => 'off',
            'min' => '-9999',
            'max' => '9999',
            'step' => '0.01'
        ]) }}
    </td>

    <td>
        {{ Form::number('items['.$itemId.'][price_with_vat]', $priceWithVat, [
            'class' => 'form-control calculations-price-with-vat',
            'autocomplete' => 'off',
            'min' => '-9999',
            'max' => '9999',
            'step' => '0.01'
        ]) }}
    </td>

    <td>
        {{ Form::number('items['.$itemId.'][quantity]', $quantity, [
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
