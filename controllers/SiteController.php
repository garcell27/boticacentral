<?php

namespace app\controllers;

use app\models\Botica;
use app\models\Panel;
use app\models\Usuarios;
use app\models\VentaSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','signup','conectar','misboticas'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function beforeAction($action)
    {
        if ($action->id == 'error')
            $this->layout = 'error.php';

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model= new Panel();
        $ventas=new VentaSearch();
        $boticas=Botica::find()->where(['tipo_almacen'=>0])->all();
        return $this->render('index',[
            'model'=>$model,
            'boticas'=>$boticas,
            'ventas'=>$ventas
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    public function actionSignup(){
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    public function actionMisboticas(){
        $boticas=Botica::find()->where(['tipo_almacen'=>0])->asArray()->all();
        return Json::encode($boticas);
    }

    public function actionConectar(){
        $post=Yii::$app->request->post();
        $formulario=$post['ConexionForm'];
        $usuario=Usuarios::findByUsername($formulario['username']);
        if($usuario->validatePassword($formulario['password']) && $usuario->idrole<=2){
            $botica=Botica::findOne($formulario['botica']);
            $respuesta=[
                'mensaje'=>true,
                'usuario'=>$usuario->getAttributes(),
                'botica'=>$botica->getAttributes()
            ];
        }else{
            $respuesta=[
                'mensaje'=>false,
                'growl'=>[
                    'message' => 'No es posible conectar con la botica, verifique usuario',
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $respuesta;
    }
    /**
     * Displays contact page.
     *
     * @return string
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    /*public function actionAbout()
    {
        return $this->render('about');
    }*/
}
