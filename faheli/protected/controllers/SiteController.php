<?php

class SiteController extends Controller {

  public $defaultAction = 'router';

  /**
   * @return array action filters
   */
  public function filters() {
    return [
      'accessControl', // perform access control for CRUD operations
      'postOnly + delete', // we only allow deletion via POST request
    ];
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   *
   * @return array access control rules
   */
  public function accessRules() {
    return [
        ['allow', // allow all users to perform 'index' and 'view' actions
            'actions' => ['login','verify','error','code','logout',
                          'phoneVerification','sendCodeAgain','register',
                          'registrationSubmitted', 'inaugurate', 'lang'],
            'users' => ['*'],
        ],
        ['allow', // allow authenticated user to perform 'create' and 'update' actions
            'actions' => ['router'],
            'users' => ['@'],
        ],
      ['allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => ['errorLogs', 'viewErrorLog'],
        'users' => ['dev', '*'],
      ],
        ['allow', // allow admin user to perform 'admin' and 'delete' actions
            'actions' => array_merge(Helpers::perms(), ['']),
            'users' => ['@'],
        ],
        ['deny', // deny all users
        'users' => ['*'],
      ],
    ];
  }


  /**
   * Declares class-based actions.
   */
  public function actionRouter() {
    // If logged in by a Registered Hajji Member, go there

    if (!empty($this->person->member))
      $this->redirect(['members/statement']);

    // Or if logged in by an Umra Pilgrim, go there
    if (!empty($this->person->umraPilgrim))
      $this->redirect(['umra/available']);

    // finally show registration options
    $this->redirect(['registration/index']);
  }

  public function actionSettings() {

    $this->layout = '//layouts/column1';

    $model = Configuration::model()->findByPk(Constants::MAIN_CONFIG_RECORD_ID);

    if (isset($_POST['Configuration'])) {
      $model->attributes = $_POST['Configuration'];
      if ($model->save()) {
        Yii::app()->user->setFlash('success', 'Configuration updated');
        Helpers::loadConfig();
      }
    }
    $this->render('update', ['model' => $model]);
  }

  /**
   * This is the action to handle external exceptions.
   */
  public function actionError() {
    if ($error = Yii::app()->errorHandler->error) {
      //ignore common errors (wrong params or unauthorized page)
      if (!(
      ($error['code'] == 400 && $error['message'] == "Your request is invalid.")
      || ($error['code'] == 403 && $error['message'] == "You are not authorized to perform this action.")
      )) {

        $errorLog = new ErrorLog();
        $errorLog->datetime = date('y-m-d H:i:s');
        $errorLog->setAttributes($error);
        $errorLog->log($error);
        $errorLog->save();
      }
      if (Yii::app()->request->isAjaxRequest || $GLOBALS['isApiRequest'])
        echo $error['message'];
      else
        $this->render('error', $error);
    }
  }

  public function actionErrorLogs() {
    $dataProvider = new CActiveDataProvider('ErrorLog', [
      'pagination' => ['pageSize' => 40],
      'sort' => [
        'attributes' => ['*'],
        'defaultOrder' => 'datetime desc',
      ]
    ]);

    $this->render('errorLogs', ['dataProvider' => $dataProvider]);
  }

  public function actionViewErrorLog($id) {
    $data = ErrorLog::model()->findByPk($id);
    if ($data != null)
      $this->render('errorLog', ['data' => $data]);
    else
      throw new Exception ('Record not found!', 404);
  }

  /**
   * Displays the contact page
   */
  public function actionContact() {
    $model = new ContactForm;
    if (isset($_POST['ContactForm'])) {
      $model->attributes = $_POST['ContactForm'];
      if ($model->validate()) {
        $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
        $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
        $headers = "From: $name <{$model->email}>\r\n" .
                "Reply-To: {$model->email}\r\n" .
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/plain; charset=UTF-8";

        mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
        Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
        $this->refresh();
      }
    }
    $this->render('contact', ['model' => $model]);
  }

  /**
   * Displays the login page
   */
  public function actionLogin() {



    $model = new LoginForm;

    // if it is ajax validation request
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {

      if (empty(CJSON::decode(CActiveForm::validate($model)))) { // Good Login
        try {
          /** @var Users $user */
          $user = Users::model()
            ->findByAttributes(['user_name' => $_POST['LoginForm']['username']]);
          $userArray = $user->attributes;
          $user->operation_log_id = 1;
          $person = $user->person;
          $person->operation_log_id = 1;
          unset($userArray['user_secret'], $userArray['login_code'], $userArray['code_expiry_time'], $userArray['last_login_datetime']);
          header('authorization: Bearer ' . Helpers::getJwt([
              'user' => $userArray, 'person' => $person->attributes
            ]));
        } catch (Exception $ex) {
          echo $ex->getMessage();
        }

      } else {
        echo CJSON::encode([]); // Incorrect Login
      }

      Yii::app()->end();
    }

    // collect user input data
    if (isset($_POST['LoginForm'])) {
      $model->attributes = $_POST['LoginForm'];

      // validate user input and redirect to the previous page if valid
      if (isset($_POST['LoginForm']['username'], $_POST['LoginForm']['code']) &&
        empty($_POST['password'])
      ) {
        $this->forward('/site/code');
      }
      if ($model->validate() && $model->login()) {
        if (Helpers::config('mobileLoginVerification')) {
          $this->forward('/site/code');
        } else {
          $this->redirect(Yii::app()->user->returnUrl);
        }
      }
    }
    // display the login form
    $this->render('login', ['model' => $model]);
  }

  public function actionLang() {

    Yii::app()->session['lang'] = Yii::app()->session['lang']=='dv'?'en':'dv';
    $this->redirect(Yii::app()->request->urlReferrer);
  }

  public function actionVerify($animate = false) {


    $this->layout = '//layouts/column2';

    $model = new PhoneVerifyForm();

    // if it is ajax validation request
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {

      if (empty(CJSON::decode(CActiveForm::validate($model)))) { // Good Login
        try {
          /** @var Users $user */
          $user = Users::model()
            ->findByAttributes(['user_name' => $_POST['LoginForm']['username']]);
          $userArray = $user->attributes;
          $user->operation_log_id = 1;
          $person = $user->person;
          $person->operation_log_id = 1;
          unset($userArray['user_secret'], $userArray['login_code'], $userArray['code_expiry_time'],
            $userArray['last_login_datetime']);
          header('authorization: Bearer ' . Helpers::getJwt([
              'user' => $userArray, 'person' => $person->attributes
            ]));
        } catch (Exception $ex) {
          echo $ex->getMessage();
        }

      } else {
        echo CJSON::encode([]); // Incorrect Login
      }

      Yii::app()->end();
    }

    // collect user input data
    if (isset($_POST['PhoneVerifyForm'])) {
      $model->attributes = $_POST['PhoneVerifyForm'];

      if ($model->validate() && $model->sendVerificationCode()) {
        Yii::app()->user->setFlash('info', H::t('site', 'codeSent'));

        $this->redirect([
          'site/phoneVerification', 'id' => $model->person->id
        ]);
      }
    }
    // display the login form
    $this->render('verify', ['model' => $model]);
  }

  public function actionRegister() {
    #region initialize
    $onlineForm = new OnlineRegistrationForm();
    $onlineForm->approved = 0;
    #endregion

    #region Handle Form Submission
    
    if (!empty($_POST['OnlineRegistrationForm'])) {


      $postedValues = $_POST['OnlineRegistrationForm'];
      $postedValues['fardHajjPerformed'] = !empty($_POST['fard_hajj_performed']);

      #region Load Form Values

      #check OTP
      $validOtp = PhoneSmsCodes::model()->findByAttributes(['phone' => $postedValues['phone_number_1'], 'code' => $_POST['otp']]);
      if (empty($validOtp)) {
        Yii::app()->user->setFlash('error',H::t('site','incorrectOtp'));
        $this->refresh();
      }

      if (!empty($person = Persons::model()->findByAttributes(['id_no' => trim($postedValues['id_no'])]))) {
        $person->dnrVerifyPerson();
        if (!empty($person->member)) {
          Yii::app()->user->setFlash('error', H::t('site','idAlreadyRegistered'));
          $this->refresh();
        }
      } else {
        $person = new Persons();
        $person->setAttributes([
          'id_no' => $postedValues['id_no'],
          'full_name_english' => $postedValues['full_name_english']
        ]);
        $person->dnrVerifyPerson();
      }

      // CVarDumper::dump($dnrData->gender_id, 10, 1);die;
      if (!$person->dnr_verified) {
        Yii::app()->user->setFlash('error', H::t('site', 'idInfoInvalid'));
        $this->refresh();
      } else {
        $postedValues['full_name_english'] = $person->full_name_english;
        $postedValues['full_name_dhivehi'] = $person->full_name_dhivehi;
        $postedValues['perm_address_english'] = $person->perm_address_english;
        $postedValues['perm_address_dhivehi'] = $person->perm_address_dhivehi;
        $postedValues['perm_address_island_id'] = $person->perm_address_island_id;
        $postedValues['d_o_b'] = $person->d_o_b;
        $postedValues['gender_id'] = $person->gender_id;
        $postedValues['country_id'] = Constants::MALDIVES_COUNTRY_ID;
      }


      $postedValues['id_card_copy'] = "not_required_anymore.png";
      $onlineForm->setAttributes($postedValues);
      #endregion

      #region Save Transaction & Audit Info
      try {
        $onlineForm->submitted_date_time = Yii::app()->params['dateTime'];
        $onlineForm->approved = 1;
        $onlineForm->hash_key = base64_encode(sha1(uniqid(), true));
        $dbTransaction = Yii::app()->db->beginTransaction();
        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_DATA_ONLINE_REGISTRATION_FORM, $onlineForm, "New
          Online Registration");

        if ($onlineForm->save()) {
          $dbTransaction->commit();

          # Log in the user
          $identity = new UserIdentity($person->id, null);
          Yii::app()->user->login($identity, (3600 * ((int)Helpers::config('rememberPeriod'))));

          # Register for Hajj
          $this->person = $person;

          #region Create New App Form & load available data
          if (!empty($this->person->member))
            $this->redirect(['registration/index']);


          #remove any existing forms          
          $this->person->registerMember($postedValues['fardHajjPerformed']);

          $this->redirect(['registration/index']);
              

        }
      } catch (CException $ex) {
        ErrorLog::exceptionLog($ex);
          Yii::app()->user->setFlash('error', H::t('site',
            'faheliRegistrationError'));
         // CVarDumper::dump($ex,10,1);die;
      }
      #endregion
    }
    #endregion

    $this->render('register', compact('onlineForm'));

  }

  public function actionInaugurate() {
    $imageLink = CHtml::link(CHtml::image(Helpers::sysUrl(Constants::IMAGES)
      . 'faheli.jpg', 'Launch Faheli Portal', ['class' => 'centered']), Yii::app()
      ->createUrl('site/verify', ['animate' => true]));
    $inaugurationPage = <<<EOT
    <html>
    <head>
      <style>
        .centered {
          position: fixed;
          top:50%;
          left:50%;
          transform: translate(-50%, -50%);
        }
      </style>
    </head>
    <body>$imageLink
    </body>
    </html>
EOT;
    echo $inaugurationPage;

  }

  public function actionSendCodeAgain($id) {
    /** @var Persons $person */
    $person = Persons::model()->findByPk($id);
    if (empty($person)
      || PersonLogin::generateLoginCode($person, $person->phone) === false
    )
      $this->redirect(['verify']);
    Yii::app()->user->setFlash('info', H::t('site','codeSent'));
    $this->redirect(['phoneVerification', 'id' => $id]);
  }

  public function actionRegistrationSubmitted($key) {
    $onlineForm = OnlineRegistrationForm::model()->findByAttributes(['hash_key'=> $key]);
    if (empty($onlineForm)) {
      throw new CHttpException(404);
    }
    $this->render('registrationSubmitted', compact('onlineForm'));
  }


  public function actionPhoneVerification($id) {


    $model = new CodeVerificationForm();
    $model->id = $id;

    // if it is ajax validation request
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {

      if (empty(CJSON::decode(CActiveForm::validate($model)))) { // Good Login
        try {
          /** @var Users $user */
          $user = Users::model()
            ->findByAttributes(['user_name' => $_POST['LoginForm']['username']]);
          $userArray = $user->attributes;
          $user->operation_log_id = 1;
          $person = $user->person;
          $person->operation_log_id = 1;
          unset($userArray['user_secret'], $userArray['login_code'], $userArray['code_expiry_time'], $userArray['last_login_datetime']);
          header('authorization: Bearer ' . Helpers::getJwt([
              'user' => $userArray, 'person' => $person->attributes
            ]));
        } catch (Exception $ex) {
          echo $ex->getMessage();
        }

      } else {
        echo CJSON::encode([]); // Incorrect Login
      }

      Yii::app()->end();
    }

    // collect user input data
    if (isset($_POST['CodeVerificationForm'])) {
      $model->attributes = $_POST['CodeVerificationForm'];
      if ($model->validate() && $model->login()) {
        $this->redirect(['site/router']);
      }
    }
    // display the login form
    $this->render('phoneVerification', ['model' => $model]);
  }


  /**
   * Logs out the current user and redirect to homepage.
   */
  public function actionLogout() {
    Yii::app()->user->logout();
    $this->redirect(Yii::app()->homeUrl);
  }

  private function sendOnlineRegistrationReceived(OnlineRegistrationForm $onlineForm) {
    $recipientName = $onlineForm->full_name_english;

    $this->layout = '//layouts/email';

    include(Yii::app()->basePath . '/extensions/PHPMailer/phpmailer.php');
    $path = realpath(Yii::app()->basePath . "/../"
        . Helpers::config(Constants::IMAGES_PATH_CONFIG_DIRECTIVE)) . '/';
    $mail = new PHPMailer(true);
    try {
      $mail->isHTML(true);
      $mail->SetFrom('admin@mhclonline.com', Helpers::config('organisationName'));
      $mail->AddAddress($onlineForm->email_address, $onlineForm->full_name_english);
      $mail->Subject =
        $onlineForm->full_name_english . ', MHCL Online Portal ah Maruh\'abaa!';

      $mail->addEmbeddedImage($path
        . 'back-ground-logo.png', 'bg-image', 'back-ground-logo.png');
      $mail->Body =
        $this->render('onlineRegistrationReceived', compact('recipientName'), true);
      $mail->send();

      mail(Constants::REGISTRATIONS_ADMIN_EMAIL, 'Online Portal Regisration Received',
        $onlineForm->full_name_english
        . ' has requested for Online Portal Registration. Please verify the registration.',
        "From: admin@mhclonline.com");

      return true;


    } catch (phpmailerException $ex) {
      ErrorLog::exceptionLog(new CException('PhpMailer Error: '
        . $ex->errorMessage()));
    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
    }

    return false;

  }


}
