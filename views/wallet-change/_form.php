<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WalletChange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wallet-change-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'entity_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wallet_id')->textInput() ?>

    <?= $form->field($model, 'change_value')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
