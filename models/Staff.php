<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Staff".
 * To automatically encrypt the user password when you save a user,
 * set the $plain_password property, not $password!!!
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $telephone
 * @property string $email
 * @property string $address
 * @property integer $role
 * @property string $user_name
 * @property string $password
 * @property string $last_login
 * @property string $auth_key
 * @property string $plain_password
 */
class Staff extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * Set this property to change the password of a user.
     * If you change this property password will be automatically encrypted
     * If you change the $password attribute you will change directly
     * the encrypted password
     */
    public $plain_password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Staff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role'], 'integer'],
            [['last_login'], 'safe'],
            [['name', 'surname', 'telephone', 'email', 'address', 'user_name', 'password', 'auth_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'surname' => Yii::t('app', 'Surname'),
            'telephone' => Yii::t('app', 'Telephone'),
            'email' => Yii::t('app', 'E-Mail'),
            'address' => Yii::t('app', 'Address'),
            'role' => Yii::t('app', 'Role'),
            'user_name' => Yii::t('app', 'User Name'),
            'password' => Yii::t('app', 'Password'),
            'last_login' => Yii::t('app', 'Last Login'),
        ];
    }

    /**
     * Checks if a password is valid for the current user
     * @param $psw the password to check
     * @return bool true if $psw is valid. False otherwise
     */
    public function isValidPassword($psw){
        return Yii::$app->getSecurity()->validatePassword($psw, $this->password) ? true : false;
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be
     *   `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: TO be implemented if we need OAuth authentication for REST
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Actions to execute automatically before a save on database
     * Since auth_key is as internal thing, not related to the specific application domain, we generate it automatically
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
            }
            if(isset($this->plain_password)){
                $this->password= \Yii::$app->getSecurity()->generatePasswordHash($this->plain_password);
                unset($this->plain_password);
            }
            return true;
        }
        return false;
    }
}
