<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @font-face {
            font-family: 'Lato';
            font-style: normal;
            font-weight: 400;
            src: local('Lato Regular'), local('Lato-Regular'), url('/assets/invoice/fonts/Lato/lato-regular.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/invoice/pdf/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/invoice/pdf/example-invoice.css') }}" />
    <title>{{ $invoice->invoice_nr }}</title>
</head>
<body>

    <div class="header">
        <div class="row">
            <div class="col-xs-4 text-center">
                <h1 class="p-a-0 m-a-0">Invoice</h1>
            </div>

            <div class="col-xs-4 text-center">
                <p>{{ $invoice->getSenderParam('company_name', 'Example company Ltd.') }}</p>
                <p>{{ $invoice->getSenderParam('registration_number', '1234567891011') }}</p>
            </div>

            <div class="col-xs-4 text-center">
                <p>{{ $invoice->getSenderParam('phone_number', '+000 00000000') }}</p>
                <p>{{ $invoice->getSenderParam('email', 'support@example.com') }}</p>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->price_with_vat }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>