<?php
declare(strict_types=1);

namespace ModalForm\View\Helper;

use Cake\Utility\Text;
use Cake\View\Helper;
use ModalForm\ModalFormPlugin;

/**
 * FormModal helper
 *
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class ModalFormHelper extends Helper
{
    /**
     * helpers
     *
     * @var array
     */
    protected array $helpers = ['Form', 'Html', 'Url'];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'element' => ModalFormPlugin::FORM_CHECKBOX,
        'modalScript' => null,
        'content' => [],
        'defaultBlock' => null,
    ];

    protected string $modalScript = <<<MODAL_SCRIPT
    $('#:target').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget)
        let modal = $(this)
        modal.find('form').prop('action', button.data('url'));
        modal.find('.message').html(button.data('confirm'));
        modal.find('.modal-title').html(button.data('title'));
    })
    MODAL_SCRIPT;

    protected string $modalScriptSubmit = <<<MODAL_SCRIPT_SUBMIT
    $('#:target').on('show.bs.modal', function(event) {
        //let button = $(event.relatedTarget)
        //let modal = $(this)
        //modal.find('form').prop('action', button.data('url'));
        //modal.find('.message').html(button.data('confirm'));
        //modal.find('.modal-title').html(button.data('title'));
        //modal.find('form').submit();
    })
    MODAL_SCRIPT_SUBMIT;

    /**
     * Creates a Bootstrap modal.
     *
     * ### Options
     *
     * - `modalScript`
     * - `element`
     * - `content`
     *      - `buttonOk`
     *      - `buttonCancel`
     *      - `textConfirm`
     *      - `label`
     * - `block`
     *
     * @param string $name The content to be wrapped by `<a>` tags.
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param array<string, mixed> $options Array of options and HTML attributes.
     * @return string|null
     */
    public function modal(string $name, array $options = []): ?string
    {
        $modalScript = $options['modalScript'] ?? $this->getConfig('modalScript') ?? $this->modalScript;
        $element = $options['element'] ?? $this->getConfig('element');
        $content = array_merge($this->defaultContentData($element), $this->getConfig('content'), $options['content'] ?? []);
        $content['label'] = $this->getLabel($content);
        $block = $options['block'] ?? $this->getConfig('defaultBlock');

        $this->Html->scriptBlock(Text::insert($modalScript, [
            'target' => $name,
        ]), ['block' => true]);

        $modal = $this->getView()->element($element, [
            'target' => $name,
            'content' => $content,
        ]);

        if (is_string($block)) {
            $this->getView()->assign($block, $modal);

            return null;
        }

        return $modal;
    }

    /**
     * Creates a link that opens a modal.
     *
     * @param string $title The link text.
     * @param mixed $url The URL the modal form will submit to.
     * @param array<string, mixed> $options Array of options and HTML attributes.
     * @return string
     */
    public function link(string $title, mixed $url = null, array $options = []): string
    {
        $options['data-url'] = $this->Url->build($url);
        $options['data-toggle'] = 'modal';
        $options['data-target'] = '#' . $options['target'];
        unset($options['target']);
        $options['data-confirm'] = $options['confirm'] ?? $options['data-confirm'] ?? null;
        unset($options['confirm']);
        $options['data-title'] = $options['title'] ?? $options['data-title'] ?? null;
        unset($options['title']);

        return $this->Html->link($title, '#', $options);
    }

    /**
     * Creates a submit button for the modal form.
     *
     * @param string|null $caption The button caption.
     * @param array<string, mixed> $options Array of options and HTML attributes.
     * @return string
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        //$options['data-url'] = $this->Url->build($options['url'] ?? null);
        //$options['data-toggle'] = 'modal';
        //$options['data-target'] = '#' . $options['target'];
        //unset($options['target']);
        //$options['data-confirm'] = $options['confirm'] ?? $options['data-confirm'] ?? null;
        //unset($options['confirm']);
        //$options['data-title'] = $options['title'] ?? $options['data-title'] ?? null;
        //unset($options['title']);

        return $this->Form->submit($caption, $options);
    }

    /**
     * Returns the default content data for a given modal element.
     *
     * @param string $element The modal element name.
     * @return array
     */
    protected function defaultContentData(string $element): array
    {
        $content = [
            'buttonOk' => __('Submit'),
            'buttonCancel' => __('Cancel'),
        ];

        switch ($element) {
            case ModalFormPlugin::FORM_PASSWORD:
                $content['label'] = __('Type your password to confirm');
                break;
            case ModalFormPlugin::FORM_CHECKBOX:
                $content['label'] = __('Check this to confirm');
                break;
            case ModalFormPlugin::FORM_CONFIRM:
                $content['buttonOk'] = __('Yes');
                $content['buttonCancel'] = __('No');
                break;
            case ModalFormPlugin::FORM_TEXT_INPUT:
                $content['textConfirm'] = 'sample_text';
                $content['label'] = function ($content) {
                    return Text::insert('Type <code>:textConfirm</code> to confirm', ['textConfirm' => $content['textConfirm']]);
                };
                break;
            case ModalFormPlugin::FORM_TIMER:
                $content['timer'] = 10; // time in seconds
                break;
        }

        return $content;
    }

    /**
     * Resolves the modal label from a string or callable.
     *
     * @param array $content The content data.
     * @return string|null
     */
    protected function getLabel(array $content): ?string
    {
        $label = $content['label'] ?? null;

        if (is_string($label)) {
            return $label;
        }

        if (is_callable($label)) {
            return $label($content);
        }

        return null;
    }
}
