<?php

namespace Modules\Invoice\Traits;

use Exception;
use Illuminate\Http\Request;
use Modules\Invoice\Models\Invoice;
use App\DataMappers\ShippingRecipient;

trait InvoiceServiceMethods
{
    /**
     * Set shipping option.
     *
     * @param \Modules\Invoice\Models\Invoice $invoice
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function service__setShippingOption(Invoice $invoice, Request $request)
    {
        $invoice->service->shipping_option_id = $request->input('shipping_option_id');
        $invoice->service->save();

        session()->flash('shippingSuccess', 'Option successfully changed!');
        return redirect(route('invoice::edit', $invoice) . '#shipping');
    }

    /**
     * Submit data to service and register new parcel.
     *
     * @param \Modules\Invoice\Models\Invoice $invoice
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function service__submitToService(Invoice $invoice, Request $request)
    {
        $shippingOption = $invoice->service->shippingOption;

        if ($shippingOption->type == 'parcel_machine') {
            $invoice->service->update([
                'shipping_option_location_id' => $request->input('shipping_option_location_id'),
            ]);
        }

        // Service fields.
        if ($request->has('serviceFields')) {
            foreach ($request->input('serviceFields', []) as $key => $value) {
                $invoice->service->fields()->updateOrCreate(
                    compact('key'), compact('value')
                );
            }
        }

        // Init handler.
        $serviceHandler = app($shippingOption->handler);
        $shippingRecipient = new ShippingRecipient($invoice);

        $psId = $invoice->service->shippingOptionLocation->fresh()->data->get('parcelshop_id');

        try {
            $parcelId = $serviceHandler->createParcel($shippingRecipient, [
                'parcels_count' => 1,
                'parcelshop_id' => $psId,
                'service_type'  => $request->input('service_type'),
                'weight'        => $invoice->service->getField('weight'),
                'order_nr'      => $invoice->invoice_nr . str_random(5), // @TODO - remove str_random.
            ]);

            if (!$parcelId) {
                session()->flash('shippingError', 'Service responded with error!');
                return redirect(route('invoice::edit', $invoice) . '#shipping');
            }

            $invoice->service->update([
                'service_side_id'    => (string)$parcelId,
                'is_sent_to_service' => true,
            ]);

            session()->flash('shippingSuccess', 'Parcel successfully registered in service - ID: ' . $parcelId);
            return redirect(route('invoice::edit', $invoice) . '#shipping');

        } catch (Exception $exception) {
            session()->flash('shippingError', 'Error from service: ' . $exception->getMessage());
            return redirect(route('invoice::edit', $invoice) . '#shipping');
        }
    }

    /**
     * Delete parcel from service side.
     *
     * @param \Modules\Invoice\Models\Invoice $invoice
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function service__deleteFromService(Invoice $invoice, Request $request)
    {
        try {
            $serviceHandler = app($invoice->service->shippingOption->handler);

            $serviceHandler->deleteParcel(
                $invoice->service->service_side_id
            );
        } catch (Exception $exception) {
            session()->flash('shippingError', 'Error from service: ' . $exception->getMessage());
            return redirect(route('invoice::edit', $invoice) . '#shipping');
        }

        $invoice->service->service_side_id = null;
        $invoice->service->is_sent_to_service = false;
        $invoice->service->save();

        session()->flash('shippingSuccess', 'Parcel successfully delete from service!');
        return redirect(route('invoice::edit', $invoice) . '#shipping');
    }

    /**
     * Get parcel label and display as response.
     *
     * @param \Modules\Invoice\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function printParcelLabel(Invoice $invoice)
    {
        if (!$invoice || !$invoice->service || !$invoice->service->service_side_id) {
            abort(404);
        }

        if (!$invoice || !$invoice->service->shippingOption || !$invoice->service->shippingOption->handler) {
            abort(404);
        }

        if (!class_exists($invoice->service->shippingOption->handler)) {
            abort(404);
        }

        $shippingOption = $invoice->service->shippingOption;
        $serviceHandler = app($shippingOption->handler);

        $stream = $serviceHandler->getParcelLabel(
            $invoice->service->service_side_id
        );

        return response()->make($stream, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="label.pdf"',
        ]);
    }
}