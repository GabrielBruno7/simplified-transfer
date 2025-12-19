<?php

namespace Domain\Transfer;

interface TransferAuthorizerInterface
{
    public function authorize(): bool;
}
