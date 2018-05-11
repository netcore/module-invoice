@php
    $hasPaymentModule = Module::has('Payment');

    if($hasPaymentModule) {
        $paymentStateOptions = \Modules\Payment\Modules\Payment::STATE_OPTIONS;
        $paymentMethodOptions = config('netcore.module-payment.states');

        $payment = $model->payments()->first();
        $paymentState = $payment ? $payment->state : 'in_process';
        $paymentMethod = $payment ? $payment->method : 'paypal';
    } else {
        $paymentStateOptions = config('netcore.module-invoice.payment_state_options', [
            'paid'   => 'Paid',
            'unpaid' => 'Unpaid'
        ]);

        $paymentMethodOptions = config('netcore.module-invoice.payment_method_options', [
            'bank_transfer' => 'Bank transfer'
        ]);

        $paymentState = $model->payment_state;
        $paymentMethod = $model->payment_method;
    }
@endphp

<div class="row">
    <div class="col-xs-offset-8 col-xs-4 text-align-right">

        <br>

        <table class="float-right">
            <tr>
                <td class="padding-10">
                    <label for="vat">VAT %</label>
                </td>
                <td>
                    <fieldset class="form-group form-group-lg {{ $errors->has('vat') ? 'form-message-light has-error has-validation-error' : '' }}">

                        {{ Form::number('vat', null, [
                            'id' => 'vat',
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'min' => '0',
                            'max' => '100',
                            'step' => '0.01',
                            'style' => 'height:32px; padding:8px 14px;',
                        ]) }}

                        @if ($errors->has('vat'))
                            <div id="validation-message-light-error" class="form-message validation-error">
                                @foreach ($errors->get('vat') as $message)
                                    {{ $message }} <br>
                                @endforeach
                            </div>
                        @endif
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td class="padding-10">
                    <label for="total_without_vat">Total without VAT</label>
                </td>
                <td>
                    <fieldset class="form-group form-group-lg">
                        {{ Form::number('', $model->total_without_vat, [
                            'id' => 'total-without-vat',
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'min' => '0',
                            'max' => '10000',
                            'step' => '0.01',
                            'style' => 'height:32px; padding:8px 14px;',
                            'disabled'
                        ]) }}
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td class="padding-10">
                    <label for="total_with_vat">Total with VAT</label>
                </td>
                <td>
                    <fieldset class="form-group form-group-lg">
                        {{ Form::number('', $model->total_with_vat, [
                            'id' => 'total-with-vat',
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'min' => '0',
                            'max' => '10000',
                            'step' => '0.01',
                            'style' => 'height:32px; padding:8px 14px;',
                            'disabled'
                        ]) }}
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td class="padding-10">
                    <label for="payment[method]">Payment method</label>
                </td>
                <td>
                    <fieldset class="form-group form-group-lg">
                        {{ Form::select('payment[method]', $paymentMethodOptions, $paymentMethod, [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'style' => 'height:32px; padding:3px 14px;'
                        ]) }}
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td class="padding-10">
                    <label for="payment[state]">Payment state</label>
                </td>
                <td>
                    <fieldset class="form-group form-group-lg">
                        {{ Form::select('payment[state]', $paymentStateOptions, $paymentState, [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'style' => 'height:32px; padding:3px 14px;'
                        ]) }}
                    </fieldset>
                </td>
            </tr>
        </table>

        <div class="clearfix"></div>

    </div>
</div>


