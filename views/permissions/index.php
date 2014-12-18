<?php
/* @var $this yii\web\View */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
/* @var $assignments yii\rbac\Permission[][] */

use \yii\helpers\Html;
use \yii\widgets\ActiveForm;

$this->title = Yii::t('app', Yii::t('app','Permissions Assignments'));
$this->params['breadcrumbs'][] = Yii::t('app','Permissions');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];

?>
<h1><?= Yii::t('app',"Permissions");?></h1>
<p><?= Yii::t('app',"From here you can assign permissions to administrative roles");?></p>
<?php
if(empty($permissions) || empty($roles)){
    echo Html::tag('p', Yii::t('app',"Please make sure there is at least one permission and one role"),['class' => 'alert alert-warning']);
}
else {
    $form = ActiveForm::begin();
    ?>
    <table>
        <thead>
            <tr>
                <th><?= Yii::t('app',"Permissions\\Roles")?></th>
            <?php
            foreach($roles as $r){
                echo Html::tag('th',$r->name);
            }
            ?>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach($permissions as $p){
            $out=Html::tag('td',$p->description);
            foreach($roles as $r){
                $out .= Html::tag('td',
                    Html::checkbox('permissions[' . $r->name . '][' . $p->name . ']',
                        array_key_exists($p->name,$assignments[$r->name]),
                        ['title' => $r->name . " | " . $p->name])
                );
            }
            echo Html::tag('tr',$out);
        }
        ?>
        </tbody>
    </table>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>
<?php
    $form->end();
}
?>