<?php
/* @var $this yii\web\View */
/* @var $roles yii\rbac\Role[] */

use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title = Yii::t('app', Yii::t('app','Administrative Roles'));
$this->params['breadcrumbs'][] = Yii::t('app','Permissions');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=\Yii::t('app',"Administrative Roles") ?></h1>
<h3><?=\Yii::t('app',"From here you can manage the user roles available in the system")?></h3>
<a href="<?=Url::to('permissions/add-role')?>" class="btn btn-success"><?=Yii::t('app',"Create New Role")?></a>
<?php
if(empty($roles)){
    echo Html::tag('p',Yii::t('app','There are no roles in the system'));
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
                Html::tag('div', $r->description, ['class' => 'role_description']),
                ['class' => 'role_title']
            );
            $Ubutton = Html::a(Yii::t('app', 'Change'), Url::to(['permissions/update-role', 'name' => $r->name]),['class' => 'btn btn-primary']);
            $Dbutton = Html::a(Yii::t('app', 'Delete'), Url::to(['permissions/delete-role', 'name' => $r->name]),['class' => 'btn btn-danger']);
            $op = Html::tag('td', $Ubutton . '&nbsp;' . $Dbutton);

            echo Html::tag('tr', $name . $op);
        }
        ?>
        </tbody>
    </table>
<?php
}
?>