<?php
declare(strict_types=1);

namespace ModalForm\Test\TestCase\Validator;

use Authentication\Identity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use ModalForm\Validator\CheckboxValidator;
use ModalForm\Validator\ConfirmValidator;
use ModalForm\Validator\PasswordValidator;
use ModalForm\Validator\TextInputValidator;

/**
 * ModalForm\Validator tests.
 */
class ValidatorsTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckboxValidator(): void
    {
        $this->assertTrue((new CheckboxValidator(new ServerRequest(), ['confirm' => '1']))->isValid());
        $this->assertFalse((new CheckboxValidator(new ServerRequest(), ['confirm' => '0']))->isValid());
        $this->assertFalse((new CheckboxValidator(new ServerRequest(), []))->isValid());
    }

    /**
     * @return void
     */
    public function testConfirmValidator(): void
    {
        $post = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'POST']]);
        $get = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'GET']]);

        $this->assertTrue((new ConfirmValidator($post, []))->isValid());
        $this->assertFalse((new ConfirmValidator($get, []))->isValid());
    }

    /**
     * @return void
     */
    public function testTextInputValidator(): void
    {
        $this->assertTrue(
            (new TextInputValidator(new ServerRequest(), ['confirm' => 'abc', 'input' => 'abc']))->isValid()
        );
        $this->assertFalse(
            (new TextInputValidator(new ServerRequest(), ['confirm' => 'abc', 'input' => 'xyz']))->isValid()
        );
    }

    /**
     * @return void
     */
    public function testPasswordValidator(): void
    {
        $user = new \ArrayObject(['password' => (new DefaultPasswordHasher())->hash('secret')], \ArrayObject::ARRAY_AS_PROPS);

        $request = (new ServerRequest(['environment' => ['REQUEST_METHOD' => 'POST']]))
            ->withAttribute('identity', new Identity($user));

        $this->assertTrue((new PasswordValidator($request, ['password' => 'secret']))->isValid());
        $this->assertFalse((new PasswordValidator($request, ['password' => 'wrong']))->isValid());
    }
}
