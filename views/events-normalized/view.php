<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use yii\helpers\Html;
use app\models\NetworkModel;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalized */

$this->params['title'] = 'Normalized Event ID: ' . $model->id;
$this->params['src_device'] = NetworkModel::getNetworkDevice($model->src_ip_network_model);
$this->params['dst_device'] = NetworkModel::getNetworkDevice($model->dst_ip_network_model);
?>

<div class="main-actions centered-horizontal">
    <?php if ($model->analyzed): ?>
        <?= Html::a("<i class='material-icons' title=\"Show charts\">show_chart</i>" . Yii::t('app', 'Get'), ['show', 'id' => $model->id], ['class' => 'btn-floating waves-effect waves-light btn-large blue'], ['title' =>'Show charts']) ?>
    <?php endif; ?>
    <?= Html::a("<i class='material-icons' title=\"Group events\">group_work</i>" . Yii::t('app', 'Get'), ['analyse', 'id' => $model->id, 'norm' => 'true'], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
    <?= Html::a("<i class='material-icons' title=\"Search clusters\">filter_alt</i>" . Yii::t('app', 'Get'), ['searchclusters', 'id' => $model->id], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
</div>

<div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">event</i>Event information</div>
<div class="events-normalized-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'datetime',
            'host',
            'cef_version',
            'cef_vendor',
            'cef_dev_prod',
            'cef_dev_version',
            'cef_event_class_id',
            'cef_name',
            'cef_severity',
            'protocol',
/*
            'src_mac',
            'dst_mac',
            'src_code',
            'dst_code',
            'src_country',
            'dst_country',
            'src_city',
            'dst_city',
            'src_latitude',
            'dst_latitude' ,
            'src_longitude',
	        'dst_longitude',
	        'request_method',
	        'request_url:ntext',
	        'request_client_application',
		//TODO add to collapsible divs
            'destination_user_name',
            'destination_user_id',
            'destination_group_name',
            'destination_group_id',
            'device_process_id',
            'source_user_privileges',
            'exec_user',
*/
            'extensions:ntext',
            'raw:ntext',
        ],
    ]) ?>
</div>

<ul class="collapsible">
    <li>
      <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">network_wifi</i>Src IP information: <?= $model->src_ip?>:<?= $model->src_port ?></div>
      <div class="collapsible-body">
        <?= DetailView::widget([
          'model' => $model,
          'attributes' => [
              'src_ip',
              'src_mac',
              'src_port',
          ],
        ]) ?>
        <ul class="collapsible">
          <li>
            <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">public</i>Geolocation information: <?= $model->src_country?></div>
            <div class="collapsible-body"><?= DetailView::widget([
              'model' => $model,
              'attributes' => [
                  'src_code',
                  'src_country',
                  'src_city',
                  'src_latitude',
                  'src_longitude',
              ],
          ]) ?></div>
          </li>
        </ul>
        <ul class="collapsible">
          <li>
            <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">laptop</i>Network model information: <?= $this->params['src_device']->description?></div>
            <div class="collapsible-body"><?= DetailView::widget([
              'model' => NetworkModel::getNetworkDevice($model->src_ip_network_model),
              'attributes' => [
                  'description', 
                  'hostname',
                  'criticality', 
                  'operation_system', 
                  'open_ports', 
                  'ports', 
                  'services', 
                  'vulnerabilities'
              ],
            ]) ?></div>
          </li>
        </ul>

    </div>
    </li>
  </ul>

  <ul class="collapsible">
    <li>
      <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">network_wifi</i>Dst IP information: <?= $model->dst_ip?>:<?= $model->dst_port?></div>
      <div class="collapsible-body">
        <?= DetailView::widget([
          'model' => $model,
          'attributes' => [
              'dst_ip',
              'dst_mac',
              'dst_port',
          ],
        ]) ?>
        <ul class="collapsible">
          <li>
            <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">public</i>Geolocation information:<?= $model->dst_country?></div>
            <div class="collapsible-body"><?= DetailView::widget([
              'model' => $model,
              'attributes' => [
                  'dst_code',
                  'dst_country',
                  'dst_city',
                  'dst_latitude',
                  'dst_longitude',
              ],
          ]) ?></div>
          </li>
        </ul>
        <ul class="collapsible">
          <li>
            <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">laptop</i>Network model information: <?= $this->params['dst_device']->description?></div>
            <div class="collapsible-body"><?= DetailView::widget([
              'model' => NetworkModel::getNetworkDevice($model->dst_ip_network_model),
              'attributes' => [
                  'description', 
                  'hostname',
                  'criticality', 
                  'operation_system', 
                  'open_ports', 
                  'ports', 
                  'services', 
                  'vulnerabilities'
              ],
            ]) ?></div>
          </li>
        </ul>
      </div>
    </li>
  </ul>

  <ul class="collapsible">
    <li>
      <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">http</i>Request information</div>
      <div class="collapsible-body"><?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'request_method',
            'request_url',
            'request_client_application',
        ],
      ]) ?></div>
    </li>
  </ul>
