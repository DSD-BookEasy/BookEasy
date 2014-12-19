<?php
/* @var $this yii\web\View */
/* @var $user app\models\Staff */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

$this->title = Yii::t('app', Yii::t('app','Setup'));
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>

<p><?= Yii::t('app',"Setup the system by registering the first user, which will receive super user rights.")?></p>

<?php
$form = ActiveForm::begin();

echo $form->errorSummary($user);

echo $form->field($user,'user_name')->textInput();

echo $form->field($user,'plain_password')->passwordInput();
echo $form->field($user,'repeat_password')->passwordInput();
?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Start Setup'), ['class' => 'btn btn-success']) ?>
    </div>
<?php
$form->end();
?>