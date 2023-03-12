<?php

declare(strict_types=1);

namespace ModalForm\Validator;

use Cake\Http\ServerRequest;

class ConfirmValidator implements ValidatorInterface
{
    protected $request;
    protected $modalForm;

    public function __construct(ServerRequest $request = null, array $modalForm) {
        $this->request = $request;
        $this->modalForm = $modalForm;
    }

    public function isValid(): bool
    {
        return $this->request->is(['post', 'path', 'delete']);
    }
}
