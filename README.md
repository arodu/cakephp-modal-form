# ModalForm plugin for CakePHP

Bootstrap modals bound to forms for CakePHP 5, with server-side confirmation
validators. A modal trigger posts a hidden `modalForm[validator]` value; the
plugin's middleware validates it and exposes the result as the `modalForm`
request attribute.

## Requirements

- PHP >= 8.2
- `cakephp/cakephp` ^5.0

## Installation

```
composer require arodu/cakephp-modal-form:^2.0
```

Load the plugin, its middleware and the helper:

```php
// src/Application.php
$this->addPlugin(\ModalForm\ModalFormPlugin::class);

// middleware queue (e.g. AppMiddlewareQueueLoader)
$middlewareQueue->add(\ModalForm\Middleware\ModalFormMiddleware::class);

// src/View/AppView.php
$this->addHelper('ModalForm.ModalForm');
```

## Usage

Render a trigger and its modal:

```php
echo $this->ModalForm->link('Delete', ['action' => 'delete', $id], [
    'target' => 'deleteModal',
    'confirm' => 'This cannot be undone',
    'title' => 'Delete record',
]);

echo $this->ModalForm->modal('deleteModal', [
    'element' => \ModalForm\ModalFormPlugin::FORM_CHECKBOX,
]);
```

Available elements:

| Element constant | Element | Validator |
|------------------|---------|-----------|
| `FORM_CHECKBOX` | `ModalForm.checkbox` | `checkbox` |
| `FORM_PASSWORD` | `ModalForm.password` | `password` |
| `FORM_TEXT_INPUT` | `ModalForm.textInput` | `textInput` |
| `FORM_CONFIRM` | `ModalForm.confirm` | `confirm` |
| `FORM_TIMER` | `ModalForm.timer` | `confirm` |

Both `modal()` and `link()` accept a `block`/`content` option to customise the
buttons and labels.

In your controller, read and check the validator:

```php
$modalForm = $this->getRequest()->getAttribute('modalForm');
if (empty($modalForm) || !$modalForm->isValid()) {
    // reject the request
}
```

## License

MIT
