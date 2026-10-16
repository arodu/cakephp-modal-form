<?php
declare(strict_types=1);

namespace ModalForm\Validator;

interface ValidatorInterface
{
    /**
     * @return bool
     */
    public function isValid(): bool;
}
