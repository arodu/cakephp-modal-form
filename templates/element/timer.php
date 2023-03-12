<?php

use ModalForm\ModalFormPlugin;

?>

<div class="modal fade" id="<?= $target ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php if ($content['title'] ?? false) : ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= $content['title'] ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?= $this->Form->create(null, ['url' => '#', 'type' => 'POST']) ?>
            <div class="modal-body">
                <p class="message"></p>
                <?= $this->Form->hidden('modalForm.validator', ['value' => ModalFormPlugin::VALIDATOR_CONFIRM]) ?>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button($content['buttonOk'] ?? __('Submit'), ['id' => 'btn-submit', 'class' => 'btn btn-primary']) ?>
                <?= $this->Form->button($content['buttonCancel'] ?? __('Close'), ['data-dismiss' => 'modal', 'type' => 'button']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    <?php $this->Html->scriptStart(['block' => true]) ?>
    $(function() {
        let submit = $('#btn-submit');
        let label = submit.text();
        let interval = null

        $('<?= '#' . $target ?>')
            .on('show.bs.modal', function(event) {
                let countdown = <?= (int)$content['timer'] ?? 10 ?>;
                submit.prop('disabled', true);
                timer()
                interval = window.setInterval(timer, 1000);

                function timer() {
                    submit.text(label + " [" + countdown + "]");
                    if (countdown <= 0) {
                        stop()
                    }
                    countdown--;
                }
            })
            .on('hidden.bs.modal', function(event) {
                stop()
            })


        function stop() {
            if (interval != null ) {
                window.clearInterval(interval);
                interval = null;
            }
            submit.text(label);
            submit.prop('disabled', false);
        }
    })
    <?php $this->Html->scriptEnd() ?>
</script>


<?php //debug($content) 
?>