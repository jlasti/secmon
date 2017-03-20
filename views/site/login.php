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
		<div class="col s12 m4 l4">
			<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

			<div class="row">
				<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
			</div>

			<div class="row">
				<?= $form->field($model, 'password')->passwordInput() ?>
			</div>

			<div class="row">
				<?= $form->field($model, 'rememberMe')->checkbox() ?>
			</div>

			<div class="row"></div>
				
			<div class="row">
				<div class="form-group">
					<?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn waves-effect waves-light', 'name' => 'login-button']) ?>
				</div>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>
	<pre>
		Username: emile.wiegand<br>
		Password: password_0		
	</pre>
</div>
