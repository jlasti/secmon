<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\forms\LoginForm */

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

$this->params['title'] = 'Login';
?>
<div class="site-login">
	<div class="row">
			<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

			<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

			<?= $form->field($model, 'password')->passwordInput() ?>

			<?= $form->field($model, 'rememberMe')->checkbox() ?>

			<div class="form-group">
				<?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn waves-effect waves-light', 'name' => 'login-button']) ?>
			</div>

			<?php ActiveForm::end(); ?>
	</div>
</div>
