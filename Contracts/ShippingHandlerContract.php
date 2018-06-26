<?php

namespace Modules\Invoice\Contracts;

interface ShippingHandlerContract
{
    /**
     * Register new parcel in shipping service.
     *
     * @param \Modules\Invoice\Contracts\ShippingRecipientContract $recipient
     * @param array $data
     * @return int|bool
     */
    public function createParcel(ShippingRecipientContract $recipient, array $data);

    /**
     * Delete parcel in shipping database.
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteParcel($id);
}
