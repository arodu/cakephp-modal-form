<?php
declare(strict_types=1);

namespace ModalForm\Test\TestCase\View\Helper;

use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use ModalForm\ModalFormPlugin;
use ModalForm\View\Helper\ModalFormHelper;
use ReflectionMethod;

/**
 * ModalForm\View\Helper\ModalFormHelper Test Case
 */
class ModalFormHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \ModalForm\View\Helper\ModalFormHelper
     */
    protected $ModalForm;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (!Plugin::getCollection()->has('ModalForm')) {
            Plugin::getCollection()->add(new ModalFormPlugin());
        }
        $request = new ServerRequest(['url' => '/tutors/index']);
        Router::setRequest($request);
        $view = new View($request);
        $this->ModalForm = new ModalFormHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ModalForm);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testLinkBuildsModalAttributes(): void
    {
        $result = $this->ModalForm->link('Delete', '/tutors/delete/1', [
            'target' => 'deleteModal',
            'confirm' => 'Are you sure?',
            'title' => 'Delete tutor',
        ]);

        $this->assertStringContainsString('href="#"', $result);
        $this->assertStringContainsString('data-toggle="modal"', $result);
        $this->assertStringContainsString('data-target="#deleteModal"', $result);
        $this->assertStringContainsString('data-url="/tutors/delete/1"', $result);
        $this->assertStringContainsString('data-confirm="Are you sure?"', $result);
        $this->assertStringContainsString('data-title="Delete tutor"', $result);
        $this->assertStringContainsString('Delete', $result);
    }

    /**
     * @return void
     */
    public function testSubmit(): void
    {
        $result = $this->ModalForm->submit('Go', ['class' => 'btn']);

        $this->assertStringContainsString('type="submit"', $result);
        $this->assertStringContainsString('Go', $result);
    }

    /**
     * @return void
     */
    public function testModalRendersElement(): void
    {
        $result = $this->ModalForm->modal('testModal', [
            'element' => ModalFormPlugin::FORM_CHECKBOX,
            'content' => ['label' => 'Confirm it'],
        ]);

        $this->assertStringContainsString('id="testModal"', $result);
        $this->assertStringContainsString('modal fade', $result);
        $this->assertStringContainsString('Confirm it', $result);
    }

    /**
     * @return void
     */
    public function testDefaultContentData(): void
    {
        $method = new ReflectionMethod(ModalFormHelper::class, 'defaultContentData');
        $method->setAccessible(true);

        $confirm = $method->invoke($this->ModalForm, ModalFormPlugin::FORM_CONFIRM);
        $this->assertSame('Yes', $confirm['buttonOk']);
        $this->assertSame('No', $confirm['buttonCancel']);

        $password = $method->invoke($this->ModalForm, ModalFormPlugin::FORM_PASSWORD);
        $this->assertSame('Type your password to confirm', $password['label']);

        $timer = $method->invoke($this->ModalForm, ModalFormPlugin::FORM_TIMER);
        $this->assertSame(10, $timer['timer']);
    }

    /**
     * @return void
     */
    public function testGetLabel(): void
    {
        $method = new ReflectionMethod(ModalFormHelper::class, 'getLabel');
        $method->setAccessible(true);

        $this->assertSame('Hello', $method->invoke($this->ModalForm, ['label' => 'Hello']));
        $this->assertNull($method->invoke($this->ModalForm, []));
        $this->assertSame(
            'Type <code>abc</code> to confirm',
            $method->invoke($this->ModalForm, ['label' => fn(array $content) => 'Type <code>' . $content['textConfirm'] . '</code> to confirm', 'textConfirm' => 'abc']),
        );
    }
}
