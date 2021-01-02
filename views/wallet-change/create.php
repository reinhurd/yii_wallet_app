<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\WalletChange */

$this->title = 'Create Wallet Change';
$this->params['breadcrumbs'][] = ['label' => 'Wallet Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-change-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
