<?php
declare(strict_types=1);

namespace ModalForm\Test\TestCase\Middleware;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use ModalForm\Middleware\ModalFormMiddleware;
use ModalForm\Validator\CheckboxValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * ModalForm\Middleware\ModalFormMiddleware Test Case
 */
class ModalFormMiddlewareTest extends TestCase
{
    /**
     * Builds a request handler that captures the request it receives.
     *
     * @return \Psr\Http\Server\RequestHandlerInterface
     */
    protected function captureHandler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {
            /**
             * @var \Psr\Http\Message\ServerRequestInterface|null
             */
            public ?ServerRequestInterface $request = null;

            /**
             * @param \Psr\Http\Message\ServerRequestInterface $request The request.
             * @return \Psr\Http\Message\ResponseInterface
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->request = $request;

                return new Response();
            }
        };
    }

    /**
     * @return void
     */
    public function testProcessAttachesValidatorAndRemovesData(): void
    {
        $request = (new ServerRequest(['environment' => ['REQUEST_METHOD' => 'POST']]))
            ->withData('modalForm', ['validator' => 'checkbox', 'confirm' => '1']);

        $handler = $this->captureHandler();
        (new ModalFormMiddleware())->process($request, $handler);

        $this->assertInstanceOf(CheckboxValidator::class, $handler->request->getAttribute('modalForm'));
        $this->assertNull($handler->request->getData('modalForm'));
    }

    /**
     * @return void
     */
    public function testProcessWithoutModalFormDataDoesNothing(): void
    {
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'GET']]);

        $handler = $this->captureHandler();
        (new ModalFormMiddleware())->process($request, $handler);

        $this->assertNull($handler->request->getAttribute('modalForm'));
    }

    /**
     * @return void
     */
    public function testProcessThrowsOnUnknownValidator(): void
    {
        $this->expectException(NotFoundException::class);

        $request = (new ServerRequest(['environment' => ['REQUEST_METHOD' => 'POST']]))
            ->withData('modalForm', ['validator' => 'does-not-exist']);

        (new ModalFormMiddleware())->process($request, $this->captureHandler());
    }
}
