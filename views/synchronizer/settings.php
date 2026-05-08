<?php
use macgyer\yii2materializecss\widgets\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['title'] = 'MISP Synchronization Settings';

// JavaScript pre dynamické IP filtre a test pripojenia
$js = <<<JS
function updateHiddenJson() {
    var filters = [];
    $('.ip-filter-input').each(function() {
        var val = $(this).val().trim();
        if (val !== '') filters.push(val);
    });
    $('#ip-filters-json').val(JSON.stringify(filters));
}
updateHiddenJson();

$('#add-ip-filter').click(function() {
    var template = $('#ip-filter-template').html();
    $('#ip-filters-list').append(template);
    updateHiddenJson();
});
$(document).on('click', '.remove-ip-filter', function() {
    $(this).closest('.ip-filter-row').remove();
    updateHiddenJson();
});
$(document).on('input', '.ip-filter-input', function() {
    updateHiddenJson();
});
$('#misp-settings-form').submit(function() {
    updateHiddenJson();
});

$('#test-connection-btn').click(function(e) {
    e.preventDefault();
    var btn = $(this);
    var originalText = btn.text();
    btn.text('Testing...').attr('disabled', true);

    $.getJSON(btn.attr('href'), function(data) {
        var panel = btn.closest('.card-panel');
        var msgSpan = panel.find('.connection-status-message');
        msgSpan.text(data.message).css('color', data.success ? 'green' : 'red');
        btn.text(originalText).attr('disabled', false);
    }).fail(function() {
        var panel = btn.closest('.card-panel');
        var msgSpan = panel.find('.connection-status-message');
        msgSpan.text('Test failed').css('color', 'red');
        btn.text(originalText).attr('disabled', false);
    });
});
JS;
$this->registerJs($js);

$status = $model->getStatusLabel();
$connStatus = $connectionStatus ?? ['success' => false, 'message' => 'Status unknown.'];
$connColor = $connStatus['success'] ? 'green' : 'red';
?>

<div class="misp-settings-form">
    <?php $form = ActiveForm::begin(['id' => 'misp-settings-form']); ?>

    <!-- Pridaný súhrn chýb -->
    <?= $form->errorSummary($model) ?>

    <div class="main-actions centered-horizontal">
        <?= Html::submitButton('Save', ['class' => 'btn red', 'style' => 'border-radius: 10px']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn grey darken-2', 'style' => 'border-radius: 10px']) ?>
    </div>

    <div class="row">
        <div class="card-panel">
            <span style="font-weight: bold;">MISP module status:</span>
            <span style="color: <?= $status['color'] ?>; margin-left: 10px;"><?= Html::encode($status['label']) ?></span>
        </div>
    </div>

    <div class="row">
        <div class="card-panel">
            <span style="font-weight: bold;">Connection to MISP:</span>
            <span class="connection-status-message" style="color: <?= $connColor ?>; margin-left: 10px;">
                <?= Html::encode($connStatus['message']) ?>
            </span>
            <?= Html::a('Test again', ['test-connection'], [
                'class' => 'btn-flat waves-effect right',
                'id' => 'test-connection-btn'
            ]) ?>
        </div>
    </div>

    <!-- Download / Export checkboxy (teraz s uncheck) -->
    <div class="row">
        <div class="input-field">
            <?= Html::checkbox('MispSettings[sync_enabled]', $model->sync_enabled, [
                'id' => 'misp-sync-enabled',
                'class' => 'filled-in',
                'uncheck' => '0'
            ]) ?>
            <label for="misp-sync-enabled">Enable download from MISP server (sync attributes)</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field">
            <?= Html::checkbox('MispSettings[export_enabled]', $model->export_enabled ?? true, [
                'id' => 'misp-export-enabled',
                'class' => 'filled-in',
                'uncheck' => '0'
            ]) ?>
            <label for="misp-export-enabled">Enable export to MISP server (send correlation events)</label>
        </div>
    </div>

    <div class="row">
        <?= $form->field($model, 'misp_url')->textInput(['maxlength' => true, 'placeholder' => 'https://misp.example.com']) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'misp_api_key')->passwordInput(['maxlength' => true, 'placeholder' => 'Your API key']) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'sync_interval')->textInput([
            'type' => 'number',
            'min' => 1,
            'value' => $model->getSyncIntervalMinutes()
        ])->hint('Interval in minutes (minimum 1)') ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'organization_name')->textInput([
            'maxlength' => true,
            'placeholder' => 'Your organization (e.g., Example Corp)'
        ])->hint('This name will be used as creator_org for locally created events.') ?>
    </div>

    <div class="row">
        <label>IP filters (excluded from export)</label>
        <p class="hint-block">Enter 1 IP address per line (e.g., 192.168.1.5/32, 192.168.1.0/24).</p>

        <div id="ip-filters-list">
            <?php foreach ($model->getIpFiltersArray() as $filter): ?>
                <div class="ip-filter-row input-field">
                    <input type="text" class="ip-filter-input" value="<?= Html::encode($filter) ?>" placeholder="e.g., 192.168.1.0/24" style="width: 80%;">
                    <button type="button" class="btn-flat remove-ip-filter"><i class="material-icons">delete</i></button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-ip-filter" class="btn"><i class="material-icons left">add</i> Add filter</button>

        <input type="hidden" name="MispSettings[ip_filters_json]" id="ip-filters-json" value="<?= Html::encode(json_encode($model->getIpFiltersArray())) ?>">
    </div>

    <!-- Atribútové typy – prepracované, bez skrytého inputu -->
    <div class="row">
        <label>Attribute types to synchronize from MISP</label>
        <p class="hint-block">Select which attribute types will be downloaded during sync.</p>
        <div class="row">
            <?php
            $selectedTypes = $model->getAttributeTypesArray();
            $allTypes = [
                'ip-src'      => 'IP Source',
                'ip-dst'      => 'IP Destination',
                'domain'      => 'Domain',
                'hostname'    => 'Hostname',
                'url'         => 'URL',
                'md5'         => 'MD5 hash',
                'sha1'        => 'SHA1 hash',
                'sha256'      => 'SHA256 hash',
                'filename'    => 'Filename',
                'email-src'   => 'Email Source',
                'email-dst'   => 'Email Destination',
            ];
            $chunkSize = 4;
            $chunks = array_chunk($allTypes, $chunkSize, true);
            foreach ($chunks as $chunk): ?>
                <div class="col s12 m3">
                    <?php foreach ($chunk as $typeValue => $typeLabel): ?>
                        <div class="input-field" style="margin-top: 0; margin-bottom: 0;">
                            <?= Html::checkbox(
                                "MispSettings[attribute_types][]",
                                in_array($typeValue, $selectedTypes),
                                [
                                    'value' => $typeValue,
                                    'id'    => "attr-$typeValue",
                                    'class' => 'filled-in',
                                ]
                            ) ?>
                            <label for="attr-<?= $typeValue ?>"><?= Html::encode($typeLabel) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <label>Export tags (added to each event exported to MISP)</label>
            <p class="hint-block">Enter one tag per line. Example: tlp:amber, type:OSINT, workflow:state="complete"</p>
            <textarea name="MispSettings[export_tags]" id="export-tags" class="materialize-textarea" rows="5"><?= implode("\n", $model->getExportTagsArray()) ?></textarea>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script type="text/template" id="ip-filter-template">
    <div class="ip-filter-row input-field">
        <input type="text" class="ip-filter-input" placeholder="e.g., 192.168.1.0/24" style="width: 80%;">
        <button type="button" class="btn-flat remove-ip-filter"><i class="material-icons">delete</i></button>
    </div>
</script>