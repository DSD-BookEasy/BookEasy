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
 * @property string $user_name
 * @property string $password
 * @property string $last_login
 * @property string $auth_key
 * @property string $plain_password
 * @property string $repeat_password
 */
class Staff extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    private $_plain_password = null;
    private $_repeat_password = null;

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
            [['last_login'], 'safe'],
            [['name', 'surname', 'telephone', 'email', 'address', 'user_name', 'password', 'auth_key'], 'string', 'max' => 255],
            [['repeat_password'], 'compare', 'compareAttribute' => 'plain_password'],
            [['plain_password'], 'safe'],
            [['user_name'], 'required']
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
            'user_name' => Yii::t('app', 'User Name'),
            'password' => Yii::t('app', 'Password'),
            'last_login' => Yii::t('app', 'Last Login'),
            'plain_password' => Yii::t('app', 'Password'),
            'repeat_password' => Yii::t('app', 'Repeat Password')
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
            return true;
        }
        return false;
    }

    /**
     * Getter for $plain_password property
     * This property is populated only if in the current execution the password has been set
     * It is not possible to retrieve the plaintext password of a user set is a previous request
     * @return string the just-set password of the user
     */
    public function getPlain_Password(){
        return $this->_plain_password;
    }

    /**
     * Setter for $plain_password property.
     * WARNING: it also sets the $password attribute with the hash of $plain_password
     * Use this to change the password of a user
     * @param string $psw the new password of the user
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setPlain_Password($psw){
        if(!empty($psw)) {
            $this->_plain_password = $psw;
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($psw);
        }
    }

    /**
     * Property getter for the repeat field in the create/edit forms
     * @return mixed
     */
    public function getRepeat_Password(){
        return $this->_repeat_password;
    }

    /**
     * Property setter for the repeat field in the create/edit forms
     * @param string $psw the password of the user. Should be the same as $_plain_password
     * @return mixed
     */
    public function setRepeat_Password($psw){
        if(!empty($psw)) {
            $this->_repeat_password = $psw;
        }
    }
}
