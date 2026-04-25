<?php

/* @var $this \yii\web\View */
/* @var $content string */

use macgyer\yii2materializecss\lib\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$isGuest = Yii::$app->user->isGuest;
$user = Yii::$app->user->identity;
$userRole = $isGuest ? '' : $user->presenter()->getMainRole();
$menuItems = $isGuest ? [] : Yii::$app->navigation->getItems();
?>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= Html::csrfMetaTags() ?>
	<title>
		<?= Html::encode($this->title) ?>
	</title>
	<?php $this->head() ?>
</head>

<body class="<?= $isGuest ? 'no-sidebar' : '' ?> preload">
	<?php $this->beginBody() ?>

	<?php if (!$isGuest): ?>
		<script>
			// Inicializovanie kompaktneho modu sidebaru
			if (window.localStorage["isCompact"] === "true") {
				document.body.classList.add("compact");
			}
		</script>
	<?php endif; ?>

	<header>
		<?php if (!$isGuest): ?>
			<a href="#" class="compact-button waves-effect preload">
				<i class="material-icons">keyboard_arrow_left</i>
			</a>
		<?php endif; ?>
		<nav class="top-nav light-blue accent-4">
			<div class="container">
				<div class="nav-wrapper">
					<a href="#" data-activates="slide-out" class="button-collapse top-nav full hide-on-large-only"><i
							class="material-icons">menu</i></a>
					<a class="page-title">
						<?= isset($this->params['title']) ? $this->params['title'] : '' ?>
					</a>
				</div>
			</div>
		</nav>
	</header>

	<main>
		<?php if (!$isGuest): ?>
			<!-- Sidebar menu -->
			<ul id="slide-out" class="side-nav fixed">
				<!-- Username a login -->
				<li>
					<div class="userView">
						<div class="background">
							<?= Html::img('@web/images/menu.png'); ?>
						</div>
						<a class="white-text profile-btn" href="<?= Url::toRoute('user/profile') ?>">
							<span class="name">
								<?= $user->username . ' (' . $userRole . ')' ?>
							</span>
							<span class="email">
								<?= $user->email; ?>
							</span>
						</a>
					</div>
				</li>

				<!-- Polozky menu -->
				<?php foreach ($menuItems as $item) {
					if ($item['visible']) {
						// Oddelovac
						if ($item['active'] === 'divider') {
							echo "<li><div class='divider'></div></li>";
							printf("<li>%s</li>", Html::a($item['label'], '', $options = ['class' => 'subheader']));
							continue;
						}
						// Bezna polozka menu
						printf(
							"<li class='%s'>%s</li>",
							($item['active'] == Yii::$app->controller->id) ? 'active' : '',
							Html::a("<i class='material-icons'>" . $item['icon'] . "</i>" . $item['label'], $item['url'], $options = ['class' => 'waves-effect', 'title' => $item['label']])
						);
					}
				}
				?>
				<li style="">
					<?= Html::beginForm(['/site/logout'], 'post') . Html::submitButton
					(
						'Logout',
						['class' => 'logout-button waves-effect', 'title' => 'Logout']
					) . Html::endForm();
					?>
				</li>
			</ul>
		<?php endif; ?>

		<!-- Main content -->
		<div class="container">
			<?= $content ?>
		</div>
	</main>

	<footer class="page-footer light-blue accent-4">
		<div class="footer-copyright">
			<div class="container">
				<div class="row">
					<div class="col l4 s12">
						&copy; FIIT STU
						<?= date('Y') ?>
					</div>
					<div class="col l4 s12">
						<a class="grey-text text-lighten-3" href="https://github.com/jlasti/secmon"><i
								class="material-icons">code</i>SecMon GitHub</a>
					</div>
					<div class="col l4 s12">
						<a class="grey-text text-lighten-3" href="mailto:talented-otters@googlegroups.com"><i
								class="material-icons">mail</i>talented-otters@googlegroups.com</a>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>