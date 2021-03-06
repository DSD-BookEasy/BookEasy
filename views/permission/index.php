<?php
/* @var $this yii\web\View */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
/* @var $assignments yii\rbac\Permission[][] */

use \yii\helpers\Html;
use \yii\helpers\Url;
use \yii\widgets\ActiveForm;

$this->title = Yii::t('app', Yii::t('app','Permissions'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Permissions'), 'url' => ['']];
?>
<h1><?= $this->title?></h1>
<p><?= Yii::t('app',"From here you can assign permissions to administrative roles.");?></p>
<a href="<?=Url::to('roles')?>" class="btn btn-primary"><?=Yii::t('app',"Manage Administrative Roles")?></a>

<?php
if(empty($permissions) || empty($roles)){
    echo Html::tag('p', Yii::t('app',"Please make sure there is at least one permission and one role"),['class' => 'alert alert-warning']);
}
else {
    $form = ActiveForm::begin();
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
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
                $out=Html::tag('td','can '.$p->description);
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
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>
<?php
    $form->end();
}
?>