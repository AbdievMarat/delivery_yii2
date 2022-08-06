<?php

namespace common\models;

use backend\models\Country;
use phpDocumentor\Reflection\File;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\image\drivers\Kohana_Image;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $avatar
 * @property string $available_countries
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $verification_token
 * @property string $password write-only password
 * @property string $role
 * @property string $password_form
 * @property File $file
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_OPERATOR = 'operator';
    const ROLE_SHOP_MANAGER = 'shop_manager';
    const ROLE_ACCOUNTANT = 'accountant';

    public $role;
    public $password_form;
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            [['email', 'avatar'], 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['role', 'password_form', 'available_countries'], 'safe'],
            [
                'file', 'file',
                'mimeTypes' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'],
                'skipOnEmpty' => true
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Name'),
            'username' => Yii::t('user', 'Username'),
            'password_hash' => Yii::t('user', 'Password hash'),
            'password_reset_token' => Yii::t('user', 'Password reset token'),
            'verification_token' => Yii::t('user', 'Verification token'),
            'email' => Yii::t('backend', 'Email'),
            'avatar' => Yii::t('user', 'Avatar'),
            'auth_key' => Yii::t('user', 'Auth key'),
            'status' => Yii::t('backend', 'Status'),
            'created_at' => Yii::t('backend', 'Created at'),
            'updated_at' => Yii::t('backend', 'Updated at'),
            'password' => Yii::t('user', 'Password'),
            'role' => Yii::t('user', 'Role'),
            'password_form' => Yii::t('user', 'Password form'),
            'avatarImage' => Yii::t('user', 'Avatar'),
            'available_countries' => Yii::t('user', 'Available countries'),
        ];
    }

    public function __construct()
    {
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'saveRole']);
    }

    /**
     * Populate roles attribute with data from RBAC after record loaded from DB
     */
    public function afterFind()
    {
        $this->role =  $this->getRole();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('backend', 'Active'),
            self::STATUS_INACTIVE => Yii::t('backend', 'Inactive'),
        ];
    }

    /**
     * @return array
     */
    public static function getRoles()
    {
        $roles = [
            self::ROLE_ADMIN => Yii::t('user', 'Admin'),
            self::ROLE_MANAGER => Yii::t('user', 'Manager'),
            self::ROLE_OPERATOR => Yii::t('user', 'Operator'),
            self::ROLE_SHOP_MANAGER => Yii::t('user', 'Shop manager'),
            self::ROLE_ACCOUNTANT => Yii::t('user', 'Accountant'),
        ];

        if(!Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id))
            unset($roles[self::ROLE_ADMIN]);

        return $roles;
    }

    /**
     * Revoke old roles and assign new if any
     */
    public function saveRole()
    {
        Yii::$app->authManager->revokeAll($this->getId());

        if($role = Yii::$app->authManager->getRole($this->role)){
            Yii::$app->authManager->assign($role, $this->getId());
        }
    }

    /**
     * Get user role from RBAC
     */
    public function getRole()
    {
        $role = Yii::$app->authManager->getRolesByUser($this->getId());
        return ArrayHelper::getColumn($role, 'name', false);
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($avatar = UploadedFile::getInstance($this, 'file')) {
            $this->avatar = strtotime('now') . '_' . Yii::$app->security->generateRandomString(6) . '.' . $avatar->extension;

            $path = Yii::getAlias('@images') . '/' . $this->avatar;
            $avatar->saveAs($path);

            $avatar_image = Yii::$app->image->load($path);
            $avatar_image->background('#fff', 0);
            $avatar_image->resize('50', '50', Kohana_Image::INVERSE);
            $avatar_image->crop('50', '50');
            $avatar_image->save(Yii::getAlias('@avatars') . '/' . $this->avatar, 90);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return string
     */
    public function getAvatarImage()
    {
        if($this->avatar)
            $path = '/images/avatars/'.$this->avatar;
        else
            $path = '/images/avatars/no_avatar.png';

        return $path;
    }

    public function getAvatarImagePreview()
    {
        if($this->avatar)
            $path = '/images/'.$this->avatar;

        return $path;
    }

    /**
     * Get country from field available_countries
     */
    public function getAvailableCountriesList()
    {
        $list = Country::find()->select('name')->where(['id' => explode(',', $this->available_countries)])->all();
        return ArrayHelper::getColumn($list, 'name', false);
    }
}
