<?php

namespace Modules\Invoice\Exceptions;

use Exception;

class PDFTemplateNotFound extends Exception
{
    protected $message = 'PDF file template was not found!';
}