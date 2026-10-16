<?php
declare(strict_types=1);

namespace ModalForm\Validator;

use Cake\Http\ServerRequest;

/**
 * Validates a text input against an expected confirmation text.
 */
class TextInputValidator implements ValidatorInterface
{
    /**
     * @var \Cake\Http\ServerRequest|null
     */
    protected $request;

    /**
     * @var array
     */
    protected $modalForm;

    /**
     * @param \Cake\Http\ServerRequest|null $request The request.
     * @param array $modalForm The modal form data.
     */
    public function __construct(?ServerRequest $request = null, array $modalForm = [])
    {
        $this->request = $request;
        $this->modalForm = $modalForm;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return ($this->modalForm['confirm'] ?? null) === ($this->modalForm['input'] ?? null);
    }
}
