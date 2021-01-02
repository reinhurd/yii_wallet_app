<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WalletSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wallet-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'money_all') ?>

    <?= $form->field($model, 'money_everyday') ?>

    <?= $form->field($model, 'money_medfond') ?>

    <?= $form->field($model, 'money_long_clothes') ?>

    <?php // echo $form->field($model, 'money_long_gifts') ?>

    <?php // echo $form->field($model, 'money_long_reserves') ?>

    <?php // echo $form->field($model, 'money_long_deposits') ?>

    <?php // echo $form->field($model, 'money_credits') ?>

    <?php // echo $form->field($model, 'last_update_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
