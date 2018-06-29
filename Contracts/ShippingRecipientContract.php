<?php

namespace Modules\Invoice\Contracts;

interface ShippingRecipientContract
{
    /**
     * Get recipient full name.
     *
     * @return string
     */
    public function getFullName(): string;

    /**
     * Get recipient company name.
     *
     * @return null|string
     */
    public function getCompanyName(): ?string;

    /**
     * Get recipient email address.
     *
     * @return string
     */
    public function getEmailAddress(): string;

    /**
     * Get recipient phone.
     *
     * @return mixed
     */
    public function getPhoneNumber(): string;

    /**
     * Get recipient country code.
     *
     * @return mixed
     */
    public function getCountryCode(): string;

    /**
     * Get recipient city.
     *
     * @return mixed
     */
    public function getCity(): string;

    /**
     * Get recipient address.
     *
     * @return string
     */
    public function getStreetAddress(): string;

    /**
     * Get recipient ZIP code.
     *
     * @param bool $full
     * @return string|int
     */
    public function getZipCode(bool $full = false);
}