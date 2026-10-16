<?php
declare(strict_types=1);

namespace ModalForm\Validator;

use Cake\Http\ServerRequest;

/**
 * Validates that the request is a state-changing request.
 */
class ConfirmValidator implements ValidatorInterface
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
        return $this->request->is(['post', 'put', 'patch', 'delete']);
    }
}
