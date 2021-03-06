<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FinanceSubRevenueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finance-sub-revenue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'advertiser_bill_id') ?>

    <?= $form->field($model, 'advertiser_id') ?>

    <?= $form->field($model, 'timezone') ?>

    <?= $form->field($model, 'revenue') ?>

    <?php // echo $form->field($model, 'om') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
