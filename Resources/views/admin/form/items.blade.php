

@php
$languages = \Netcore\Translator\Helpers\TransHelper::getAllLanguages();
$configuredInvoiceItemVariables = config('netcore.module-invoice.invoice_item_variables', []);
@endphp

<h3>Items</h3>

<table id="invoice-items-table">
    <tr>
        <th>Name</th>
        @foreach($configuredInvoiceItemVariables as $configuredVariable)
            <th>
                {{ ucfirst($configuredVariable) }}
            </th>
        @endforeach
        <th>Price without VAT</th>
        <th>Price with VAT</th>
        <th>Quantity</th>
        <th></th>
    </tr>

    @foreach($model->items as $item)
        @include('invoice::admin.form._item_template')
    @endforeach

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        @foreach($configuredInvoiceItemVariables as $configuredVariable)
        <td></td>
        @endforeach
        <td>
            <a class="btn btn-xs btn-success full-width max-width-100" id="add-invoice-item">
                Add item
            </a>
        </td>
    </tr>
</table>


<script type="text/template" id="invoice-item-template">
    @include('invoice::admin.form._item_template', ['item' => null])
</script>
