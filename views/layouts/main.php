<?php

use app\assets\AppAsset;
use yii\bootstrap5\Html;
  
AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '180x180', 'href' => Yii::getAlias('@web/portal-assets/img/favicons/apple-touch-icon.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '32x32', 'href' => Yii::getAlias('@web/portal-assets/img/favicons/favicon-32x32.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '16x16', 'href' => Yii::getAlias('@web/portal-assets/img/favicons/favicon-16x16.png')]);

$session = Yii::$app->session;
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= Html::encode($this->title) ?></title>
  <?php $this->head() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <?php $this->beginBody() ?>

  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if (!Yii::$app->user->isGuest): ?>
            <li class="nav-item">
                <span class="nav-link text-muted">
                    <i class="fas fa-user-circle"></i>
                    <?= Html::encode(Yii::$app->user->identity->name) ?>
                    <span class="badge badge-secondary ml-1"><?= Html::encode(Yii::$app->user->identity->role) ?></span>
                </span>
            </li>
            <li class="nav-item">
                <?= Html::a('<i class="fas fa-sign-out-alt"></i> Logout', ['site/logout'], ['class' => 'nav-link']) ?>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php include 'includes/sidebar.php' ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark"> <?= $this->title ?></h1>
            </div>

            <div class="col-sm-6">
                <?= yii\bootstrap5\Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'options' => ['class' => 'float-sm-right'],
                    'homeLink' => [
                        'label' => Yii::$app->name,
                        'url' => '/portal',
                    ],
                ]) ?>
            </div>
          </div>
        </div>
      </div>

      <div class="content">
        <div class="container-fluid">
          <?= $content ?>
        </div>
      </div>
    </div>


    <footer class="main-footer">
      <strong>Copyright &copy; <?= date('Y') ?> <a href="/portal"><?= Yii::$app->name ?></a>.</strong> All rights reserved.
    </footer>
  </div>



  <?php
  if ($session->hasFlash('success')) {
      $this->registerJs("showMessage('" . $session->getFlash('success') . "', 'success');", \yii\web\View::POS_END);
  } elseif ($session->hasFlash('error')) {
      $this->registerJs("showMessage('" . $session->getFlash('error') . "', 'error');", \yii\web\View::POS_END);
  }
  ?>
  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>