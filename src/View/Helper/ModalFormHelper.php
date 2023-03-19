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
    protected $helpers = ['Form', 'Html', 'Url'];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'element' => ModalFormPlugin::FORM_CHECKBOX,
        'modalScript' => null,
        'content' => [],
        'defaultBlock' => null,
    ];

    protected $modalScript = <<<MODAL_SCRIPT
    $('#:target').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget)
        let modal = $(this)
        modal.find('form').prop('action', button.data('url'));
        modal.find('.message').html(button.data('confirm'));
        modal.find('.modal-title').html(button.data('title'));
    })
    MODAL_SCRIPT;

    protected $modalScriptSubmit = <<<MODAL_SCRIPT_SUBMIT
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

    public function link($title, $url = null, array $options = []): string
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

    public function submit($caption = null, array $options = []): string
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

    protected function defaultContentData($element): array
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

    protected function getLabel($content): ?string
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
