<?php

/* @var $this \yii\web\View */
/* @var $content string */

use macgyer\yii2materializecss\lib\Html;
use macgyer\yii2materializecss\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

$isGuest = Yii::$app->user->isGuest;
$user = Yii::$app->user->identity;
$userRole = $isGuest ? '' : $user->presenter()->getMainRole();
$menuItems = $isGuest ? [] : Yii::$app->navigation->getItems();
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
		      		<a href="#" data-activates="slide-out" class="button-collapse top-nav full hide-on-large-only"><i class="material-icons">menu</i></a>
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
			<li>
				<div class="userView">
				    <div class="background purple accent-4">
				    </div>
				    <a><span class="white-text name"><?= $user->username . ' (' . $userRole . ')' ?></span></a>
				    <a><span class="white-text email"><?= $user->email; ?></span></a>
					<?php
						printf("<span>%s</span>", Html::beginForm(['/site/logout'], 'post')
								. Html::submitButton(
									'Logout',
									['class' => 'white-text btn-flat']
								)
								. Html::endForm());
					?>
			    </div>
    		</li>

			<!-- Polozky menu -->
			<?php foreach($menuItems as $item)
			{
				if($item['visible'])
				{  
					// Oddelovac
					if($item['active'] === 'divider') {
						echo "<li><div class='divider'></div></li>";
						printf("<li>%s</li>", Html::a($item['label'], '',$options = ['class' => 'subheader' ]));
						continue;
					}
					// Bezna polozka menu
					printf("<li class='%s'>%s</li>",
						($item['active'] == Yii::$app->controller->id) ? 'active' : '',
						Html::a("<i class='material-icons'>" . $item['icon'] . "</i>" . $item['label'], $item['url'], $options = ['class' => 'waves-effect' ]));
				}
			}
			?>
		</ul>
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
