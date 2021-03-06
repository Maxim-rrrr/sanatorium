<?php

namespace app\controllers;

use app\helpers\DateHelper;
use app\models\Customer;
use app\models\Diet;
use app\models\Housing;
use app\models\HousingScheme;
use app\models\MedicalCard;
use app\models\Order;
use app\models\Service;
use app\models\SignupForm;
use app\models\Treatment;
use app\models\TreatmentSchedule;
use app\models\User;
use Mpdf\Mpdf;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
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
	    if (Yii::$app->user->isGuest) {
        	return $this->redirect('/site/login');
        }
	    
	    return $this->redirect('/site/cabinet');
    }

    /**
     * Login action.
     *
     * @return Response|string
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

        $model->password = '';
        return $this->render('login', compact('model'));
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup(){
	    if (!Yii::$app->user->isGuest) {
		    return $this->goHome();
	    }
	    $model = new SignupForm();
	    if($model->load(\Yii::$app->request->post()) && $model->validate()){
		    $user = new User();
		    $user->username = $model->username;
		    $user->password = \Yii::$app->security->generatePasswordHash($model->password);
		    if($user->save()){
			    return $this->goHome();
		    }
	    }
	
	    return $this->render('signup', compact('model'));
	}
	
	public function actionCabinet()
	{
		$housings = Housing::find()->all();
		$customers = Customer::find()->all();
		$housingScheme = HousingScheme::find()->all();
		$services = Service::find()->all();
		$medicalCards = MedicalCard::find()->all();
		$diets = Diet::find()->all();
		$treatments = Treatment::find()->all();
		$scheduleBooking = [];
		/** @var HousingScheme $hs */
		foreach ($housingScheme as $hs) {
			$scheduleBooking[$hs->housing_id][$hs->room] = [
				'capacity_room' => (int) $hs->room_type,
				'reserved_seats' => 0
			];
		}
		/** @var Order[] $orders */
		$orders = Order::find()->all();
		
		foreach ($orders as $order) {
			$scheduleBooking[$order->housing_id][$order->room]['reserved_seats']++;
		}
		
		return $this->render('cabinet', [
			'housings' => $housings,
			'customers' => $customers,
			'scheduleBooking' => $scheduleBooking,
			'services' => $services,
			'medicalCards' => $medicalCards,
			'diets' => $diets,
			'treatments' => $treatments
		]);
	}
	
	/**
	 * Путевка
	 * @throws HttpException
	 * @throws \Mpdf\MpdfException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionPermit()
	{
		$params = \Yii::$app->request->get();
		
		if (empty($params['customer_id'])) {
			throw new HttpException(400,"Обязательный параметр ID клиента");
		}
		
		$customer = Customer::findOne(['id' => $params['customer_id']]);
		
		if (!$customer) {
			throw new HttpException(400,"Клиент с не найден");
		}
		
		$mpdf = new Mpdf(
			[
				'mode' => 'utf-8',
				'format' => 'A4',
				'orientation' => 'L'
			]
		);
		
		$mpdf->WriteHTML(
			$this->renderPartial('permit',[
				'customer' => $customer,
				'housing' => Housing::findOne(['id' => $customer->order->housing_id])
			])
		);
		
		$mpdf->Output();
	}
}
