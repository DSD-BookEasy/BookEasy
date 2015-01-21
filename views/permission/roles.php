<?php
/* @var $this yii\web\View */
/* @var $roles yii\rbac\Role[] */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title = Yii::t('app', Yii::t('app','Administrative Roles'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Permissions'), 'url' => 'index'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
<h1><?=$this->title ?></h1>
<p><?=\Yii::t('app',"From here you can manage the user roles available in the system. Roles represent a collection of permissions.")?></p>
<a href="<?=Url::to('add-role')?>" class="btn btn-success"><?=Yii::t('app',"Create new role")?></a>
<?php
if(empty($roles)){
    echo Html::tag('p',Yii::t('app','There are no roles in the system.'),['class' => 'alert alert-info']);
}
else {
    ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><?= \Yii::t('app', "Role Name"); ?></th>
            <th><?= \Yii::t('app', "Operations") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($roles as $r) {
            $name = Html::tag('td', $r->name .
                Html::tag('div', $r->description, ['class' => 'small']),
                ['class' => 'role_title']
            );
            $Ubutton = Html::a(Yii::t('app', 'Change'), Url::to(['permission/update-role', 'name' => $r->name]),['class' => 'btn btn-primary']);
            $Dbutton = Html::a(Yii::t('app', 'Delete'), Url::to(['permission/delete-role', 'name' => $r->name]),['class' => 'btn btn-danger']);
            $op = Html::tag('td', $Ubutton . '&nbsp;' . $Dbutton);

            echo Html::tag('tr', $name . $op);
        }
        ?>
        </tbody>
    </table>
<?php
}
?>