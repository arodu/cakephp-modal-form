<?php
declare(strict_types=1);

namespace ModalForm\Validator;

use Cake\Http\ServerRequest;

/**
 * Validates a checkbox confirmation.
 */
class CheckboxValidator implements ValidatorInterface
{
    /**
     * @var \Cake\Http\ServerRequest|null
     */
    protected ?ServerRequest $request = null;

    /**
     * @var array
     */
    protected array $modalForm;

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
        return (bool)($this->modalForm['confirm'] ?? false);
    }
}
