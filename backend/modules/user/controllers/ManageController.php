<?php

namespace backend\modules\user\controllers;

use common\models\User;
use backend\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ManageController implements the CRUD actions for User model.
 */
class ManageController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['admin', 'manager', 'operator'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'drop-image'],
                            'roles' => ['admin', 'manager'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['admin'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->setPassword($model->password_form);
                $model->generateAuthKey();
                $model->generateEmailVerificationToken();

                if($model->available_countries){
                    $model->available_countries = implode(',', $model->available_countries);
                }

                if($model->save()){
                    if($role = Yii::$app->authManager->getRole($model->role)){
                        Yii::$app->authManager->assign($role, $model->getId());
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if ($id == 1 && !Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
            throw new HttpException(403, 'Forbidden');
        }
        else{
            $model = $this->findModel($id);
            $model->available_countries = explode(',', $model->available_countries);

            if ($this->request->isPost && $model->load($this->request->post())) {
                if($model->available_countries){
                    $model->available_countries = implode(',', $model->available_countries);
                }
                if ($model->password_form != '') {
                    $model->setPassword($model->password_form);
                    $model->removePasswordResetToken();
                    $model->generateAuthKey();
                }

                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model && $model->avatar) {
            $path_origin_image = Yii::getAlias('@images') . '/' . $model->avatar;
            if(file_exists($path_origin_image)){
                unlink($path_origin_image);
            }
            $path_small_image = Yii::getAlias('@avatars') . '/' . $model->avatar;
            if(file_exists($path_small_image)){
                unlink($path_small_image);
            }
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     */
    public function actionDropImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = $this->request->post();
        $id = $post['key'];

        $model = $this->findModel($id);
        if ($model) {
            $path_origin_image = Yii::getAlias('@images') . '/' . $model->avatar;
            if(file_exists($path_origin_image)){
                unlink($path_origin_image);
            }
            $path_small_image = Yii::getAlias('@avatars') . '/' . $model->avatar;
            if(file_exists($path_small_image)){
                unlink($path_small_image);
            }

            $model->avatar = '';
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Image deleted'));
        } else {
            Yii::$app->session->setFlash('warning', Yii::t('backend', 'No file attached'));
        }
    }
}
