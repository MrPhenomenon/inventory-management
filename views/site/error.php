<?php

use yii\helpers\Html;

$this->title = $name ?? 'Error'; // Fallback to 'Error' if $name is not set
?>
<div class="container text-center mt-5">
    <div class="site-error">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message ?? 'An error occurred.')) ?>
        </div>

        <p>
            The above error occurred while the Web server was processing your request.
        </p>
        <p>
            Please contact us if you think this is a server error. Thank you.
        </p>

    </div>
</div>