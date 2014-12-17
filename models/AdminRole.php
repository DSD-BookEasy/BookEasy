<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use \yii\rbac\Role;

/**
 * This is just a wrapper for [[\yii\rbac\Role]]
 * To make easier to work it roles in forms
 *
 * @property string $name
 * @property string $description
 */
class AdminRole extends \yii\base\Model
{
    private $role;

    /**
     * @param Role $r
     * @throws ErrorException
     */
    public function __construct($r){
        parent::__construct();
        if($r instanceof Role || $r===null) {
            $this->role=$r;
        }
        else{
            throw new ErrorException("The passed parameter is not a Role");
        }
    }

    public function getName(){
        if($this->role!==null) {
            return $this->role->name;
        }
        else{
            return '';
        }
    }

    public function getDescription(){
        if($this->role!==null) {
            return $this->role->description;
        }
        else{
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required']
        ];
    }
}
