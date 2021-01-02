<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Wallet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wallet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'money_all')->textInput() ?>

    <?= $form->field($model, 'money_everyday')->textInput() ?>

    <?= $form->field($model, 'money_medfond')->textInput() ?>

    <?= $form->field($model, 'money_long_clothes')->textInput() ?>

    <?= $form->field($model, 'money_long_gifts')->textInput() ?>

    <?= $form->field($model, 'money_long_reserves')->textInput() ?>

    <?= $form->field($model, 'money_long_deposits')->textInput() ?>

    <?= $form->field($model, 'money_credits')->textInput() ?>

    <?= $form->field($model, 'last_update_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
