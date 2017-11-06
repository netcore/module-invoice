<?php

namespace Modules\Invoice\Repositories;

use Modules\Invoice\Exceptions\PDFTemplateNotFound;

class InvoiceRepository
{
    /**
     * VAT amount
     *
     * @var int
     */
    protected $vat = 21;

    protected $sender;
    protected $receiver;

    /**
     * Override VAT
     *
     * @param int $vat
     * @return $this
     */
    public function setVat(int $vat)
    {
        $this->vat = $vat;

        return $this;
    }

    public function setSender(array $data)
    {
        $this->sender = $data;

        return $this;
    }

    public function setReceiver(array $data)
    {
        $this->receiver = $data;

        return $this;
    }

    public function make(...$args)
    {
        dd($args);
    }
}