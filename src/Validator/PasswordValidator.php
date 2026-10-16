<?php
declare(strict_types=1);

namespace ModalForm\Validator;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Http\ServerRequest;

/**
 * Validates the password of the current identity.
 */
class PasswordValidator implements ValidatorInterface
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
        $user = $this->request->getAttribute('identity')->getOriginalData();

        return (new DefaultPasswordHasher())->check($this->modalForm['password'] ?? '', $user->password);
    }
}
