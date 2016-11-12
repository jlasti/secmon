<?php

/* @var $this \yii\web\View */
/* @var $content string */

use macgyer\yii2materializecss\lib\Html;
use macgyer\yii2materializecss\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

$isGuest = Yii::$app->user->isGuest;

if(Yii::$app->user->isGuest)
{
	$menuItems = [['label' => 'Login', 'url' => ['/site/login']]];
}
else
{
	$menuItems = Yii::$app->navigation->getItems();
}
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>

<body class="<?= $isGuest ? 'no-sidebar':'' ?>">
<?php $this->beginBody() ?>

<header>
	  <nav class="top-nav">
		    <div class="container">
		      	<div class="nav-wrapper">
		      		<a class="page-title"><?= isset($this->params['title']) ? $this->params['title'] : '' ?></a>
		  		</div>
		    </div>
	  </nav>
</header>

<main>
	<?php if(!$isGuest): ?>
		<!-- Sidebar menu -->
		<ul id="slide-out" class="side-nav fixed">
			<!-- Username a login -->
			<?php
				echo sprintf("<li>%s</li>", Html::beginForm(['/site/logout'], 'post')
						. Html::submitButton(
							'Logout (' . Yii::$app->user->identity->username . ')',
							['class' => 'btn btn-link']
						)
						. Html::endForm());
			?>

			<!-- Polozky menu -->
			<?php foreach($menuItems as $item)
			{
				if($item['visible'])
				{  
					echo sprintf("<li class='%s'>%s</li>",
						($item['active'] == Yii::$app->controller->id) ? 'active' : '',
						Html::a($item['label'], $item['url'], $options = ['class' => 'waves-effect' ]));
				}
			}
			?>
		</ul>
	    <a href="#" data-activates="slide-out" class="button-collapse top-nav full hide-on-large-only"><i class="material-icons">menu</i></a>
    <?php endif; ?>

    <!-- Main content -->
	<div class="container">
		<?= $content ?>
	</div>
</main>

<footer class="page-footer">
	<div class="container">
        <div class="row">
		    <div class="col l6 s12">
		        <h5 class="white-text">Footer Content</h5>
		        <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
		    </div>
  			<div class="col l4 offset-l2 s12">
	            <h5 class="white-text">Links</h5>
	            <ul>
	              <li><a class="grey-text text-lighten-3" href="https://team14-16.studenti.fiit.stuba.sk/">SecMon</a></li>
	              <li><a class="grey-text text-lighten-3" href="mailto:talented-otters@googlegroups.com">talented-otters@googlegroups.com</a></li>
	            </ul>
          	</div>
        </div>
  	</div>
  	<div class="footer-copyright">
		<div class="container">
			&copy; Tallented otters <?= date('Y') ?>
		</div>
	</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
