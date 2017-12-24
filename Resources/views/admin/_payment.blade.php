@if($payment = $row->payments->first())
    @php
        $paymentStateOptions = \Modules\Payment\Modules\Payment::STATE_OPTIONS;
        $state = array_get($paymentStateOptions, $payment->state);

        $paymentMethodOptions = config('netcore.module-payment.states');
        $method = array_get($paymentMethodOptions, $payment->method);
    @endphp
    {{ ucfirst($state) }} ({{ ucfirst($method) }})
@endif
