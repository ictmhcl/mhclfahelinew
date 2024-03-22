<?php

class RegistrationController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to
   *      '//layouts/column2', meaning using two-column layout. See
   *      'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column2';
  public $defaultAction = 'index';

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
   * @return array access control rules
   */
  public function accessRules() {
    return [
      ['allow', // allow all users to perform 'index' and 'view' actions
        'actions' => [''],
        'users' => ['*'],
      ],
      ['allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => ['index','hajjRegistrationSubmitted'],
        'users' => ['@'],
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
   * Displays a particular model.
   *
   * @param integer $id the ID of the model to be displayed
   */
  public function actionView($id) {
    $this->render('view', [
      'model' => $this->loadModel($id),
    ]);
  }

  public function actionMarkIncomplete($id) {
    $model = $this->loadModel($id);
    $model->state_id = Constants::APPLICATION_INCOMPLETE;
    $dbTransaction = Yii::app()->db->beginTransaction();
    $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_EDIT,
      ClientAudit::AUDIT_DATA_APPLICATION_FORM, $model);

    $model->save(false);
    $dbTransaction->commit();
    $this->redirect(['registration/verify']);
  }

  public function actionVerifyForm($id) {

    #region Load Model and create/load associated Verification Model
    /** @var ApplicationFormsHelper $appForm */
    $appForm = $this->loadModel($id);

    // Generate or Load Verification Model
    /** @var ApplicationFormVerifications $verifyModel */
    $verifyModel = ApplicationFormVerifications::model()
      ->findByAttributes(['application_form_id' => $appForm->id]);
    if (is_null($verifyModel)) {
      $verifyModel = new ApplicationFormVerifications();
      $verifyModel->application_form_id = $appForm->id;
      $dbTransaction = Yii::app()->db->beginTransaction();
      $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
        ClientAudit::AUDIT_DATA_APPLICATION_VERIFICATION, $verifyModel);

      $verifyModel->save(false);
      $dbTransaction->commit();
    }
    #endregion

    #region Handle Verification Form submission
    if (!empty($_POST['ApplicationFormVerifications'])) {

      $verifyModel->setAttributes($_POST['ApplicationFormVerifications']);
      if (!$verifyModel->applicant_verified)
        Yii::app()->user->setFlash('error',
          'Form is NOT Verified! Check again!');

      else {

        #region Save Verification Transaction & Audits
        $dbTransaction = Yii::app()->db->beginTransaction();
        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
          ClientAudit::AUDIT_DATA_APPLICATION_VERIFICATION, $verifyModel);
        $verifyModel->save();
        $dbTransaction->commit();
        #endregion

        #region Record Member Registration
        $newMemberModel = Members::model()->findByPk($appForm->registerMember());
        #endregion

        #region Set User Messages and redirect to Verification List
        if (!empty($newMemberModel)) {
          Yii::app()->user->setFlash('success', 'Application form of ' .
            $appForm->applicant_full_name_english . ' has been accepted!'
            . ' A new member record has been created.
            Currently it is PENDING PAYMENT COLLECTION. Membership Number is
            <strong>' . $newMemberModel->MHC_ID . '</strong>');
        } else {
          Yii::app()->user->setFlash('error', 'There has been an error! The
            applicant form seems already registered. Please report to system'
            . ' administrator immediately!');
        }
        $this->redirect(['registration/verify']);
        #endregion
      }
    }
    #endregion

    #region Go to view
    $this->render('verify', [
      'appForm' => $appForm,
      'verifyModel' => $verifyModel,
    ]);
    #endregion
  }



  public function actionIndex() {

    
    if ($this->person->member)
      $this->redirect(['member/statement']);
    if ($this->person->hajjApplicationForm)
      $this->redirect(['registration/hajjRegistrationSubmitted']);
    $docsArray = ['application_form', 'mahram_document'];

    #region Create New App Form & load available data
    $appForm = new ApplicationFormsHelper();
    $appForm->application_date = Yii::app()->params['date'];

    if (empty($this->person))
      throw new CHttpException(403);

    if (!empty($this->person->member))
      throw new CHttpException(404);

    // load personal information from currently logged in person
    $appForm->setAttributes([
      'applicant_full_name_english' => $this->person->full_name_english,
      'applicant_full_name_dhivehi' => $this->person->full_name_dhivehi,
      'id_no' => $this->person->id_no, 'd_o_b' => $this->person->d_o_b,
      'applicant_gender_id' => $this->person->gender_id,
      'country_id' => $this->person->country_id,
      'perm_address_island_id' => $this->person->perm_address_island_id,
      'perm_address_english' => $this->person->perm_address_english,
      'perm_address_dhivehi' => $this->person->perm_address_dhivehi,
      'phone_number_1' => $this->person->phone,
      'email_address' => $this->person->email,
      'id_copy' => $this->person->idCopy,
    ]);
    #endregion

    #region Handle form submission
    if (isset($_POST['ApplicationFormsHelper'])) {

      #region Load Form Values
      // load submitted values except file values (which are empty anyway)
      $postedValues = $_POST['ApplicationFormsHelper'];


      foreach ($docsArray as $doc) unset($postedValues[$doc]);
      $appForm->setAttributes($postedValues);
      #endregion

      #region Handle files
      $modelClass = get_class($appForm);

      // Iterate file inputs recieved
      foreach ($_FILES[$modelClass]['name'] as $fileAttribute => $fileName) {
        $error = $_FILES[$modelClass]['error'][$fileAttribute];

        // if file was not collected by the system
        if ($error > 0) {

          // if file was not provided by user
          if (empty($fileName)) {

            // add error if this is a required file
            $appForm->validate([$fileAttribute], false);
          } else { // if file was uploaded but rejected
            $appForm->addError($fileAttribute,
              $appForm->getAttributeLabel($fileAttribute) . ' (' .
              $_FILES[$modelClass]['name'][$fileAttribute] .
              ')' . H::t('site', 'fileTooLarge'));
          }
        } else { // file was collected by system
          // unique file name
          $fileName = uniqid() . '_' . $appForm->id_no . '_' . $fileName;

          $acceptFile = true; // by default
          // check if a file already exists, and if so get path & delete it;
          // do no accept file only if current file could be deleted!
          if (!empty($appForm[$fileAttribute]) &&
            !Helpers::deleteUploadedFile($appForm[$fileAttribute])
          ) {
            $acceptFile = false; // Do not accept file
            Yii::app()->user->setFlash('error', H::t('site', 'cannotReplaceFile'));
          }

          if ($acceptFile) {
            $appForm[$fileAttribute] = $fileName;
            move_uploaded_file($_FILES[$modelClass]['tmp_name'][$fileAttribute],
              Yii::app()->params['uploadPath'] . $fileName);
          }
        }
      }
      #endregion

      #region Update Application Status
      $appForm->state_id = Constants::APPLICATION_PENDING_VERIFICATION;
      #endregion

      #region Save Transaction & Audit Info
      $dbTransaction = Yii::app()->db->beginTransaction();
      $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
        ClientAudit::AUDIT_DATA_APPLICATION_FORM, $appForm, "New Application");
      if ($appForm->save())
      {
        $dbTransaction->commit();
        Helpers::textMessage($this->person->phone, $this->person->full_name_english
          . H::t('hajj', 'hajjRegistrationReceivedText'));
        Helpers::textMessage(Helpers::adminMobile(), 'Hajj Registration '
          . 'received. ID: ' . $this->person->id_no . ', Name: '
          . $this->person->full_name_english);

        $this->redirect(['hajjRegistrationSubmitted']);
      }
      #endregion


    }
    #endregion

    #region Go to view
    $this->render('create', [
      'formTitle' => H::t('hajj', 'registerForHajj'),
      'appForm' => $appForm,
    ]);
    #endregion
  }

  public function actionHajjRegistrationSubmitted() {
    if (empty($this->person->member))
      $this->render('hajjRegistrationSubmitted');
    else
      $this->redirect(['members/statement']);
  }

  /**
   * Manages all models.
   */
  public function actionIncomplete() {
    $model = new ApplicationForms();
    $criteria = new CDbCriteria();
    $criteria->condition = 'state_id = ' . Constants::APPLICATION_INCOMPLETE;
    $dataProvider = new CActiveDataProvider('ApplicationFormsHelper', [
      'criteria' => $criteria,
      'pagination' => [
        'pageSize' => Helpers::config('pageSize'),
      ],
    ]);

    $this->render('formList', [
      'formTitle' => 'Incomplete Registrations',
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Manages all models.
   */
  public function actionPayment() {
    $criteria = new CDbCriteria();
	$criteria->with = ['person', 'applicationForm'];
    $criteria->condition = 't.state_id = ' . Constants::MEMBER_PENDING_FIRST_PAYMENT;
    $dataProvider = new CActiveDataProvider('Members', [
      'criteria' => $criteria,
      'pagination' => [
        'pageSize' => Helpers::config('pageSize'),
      ],
	  'sort' => ['defaultOrder' => 'person.id_no', 'attributes'=>['person.id_no', 'mhc_no', 'person.full_name_english', 'applicationForm.application_date']]
    ]);

    $this->render('regPayment', [
      'formTitle' => 'Registrations Pending Payment',
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Manages all models.
   */
  public function actionVerify() {

    $criteria = new CDbCriteria();
    $criteria->condition = 'state_id = ' . Constants::APPLICATION_PENDING_VERIFICATION;
    $dataProvider = new CActiveDataProvider('ApplicationFormsHelper', [
      'criteria' => $criteria,
      'pagination' => [
        'pageSize' => Helpers::config('pageSize'),
      ],
    ]);

    $this->render('formList', [
      'formTitle' => 'Registrations Pending Verification',
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionRegFee($id) {

    $memberTransaction = new MemberTransactions();
    $memberTransaction->member_id = (int)$id;
    $member = $memberTransaction->member;
    $applicationForm = $member->applicationForm;

    if ($member->state_id != Constants::MEMBER_PENDING_FIRST_PAYMENT) {
      Yii::app()->user->setFlash('error', 'Member has already made first payment!');
      $this->redirect(['view', 'id' => (int)$id]);
    } else {

      if (isset($_POST['MemberTransactions'])) {
        if (!isset($_POST['feeCollected'])) {
          Yii::app()->user->setFlash('error',
            ' You have to confirm collection by marking the checkbox marked
            "Payment Received".');
        } else {

          $oldBal = 0;

          $memberTransaction->setAttributes($_POST['MemberTransactions']);
          $memberTransaction->member_id = (int)$id;
          $memberTransaction->transaction_time = date('Y-m-d H:i:s');
          $memberTransaction->transaction_type_id =
            Constants::TRANSACTION_TYPE_REGISTRATION_FEE;
          $memberTransaction->description_english =
            Helpers::config('registrationFeeDescriptionEnglish');
          $memberTransaction->description_dhivehi =
            Helpers::config('registrationFeeDescriptionDhivehi');
          $memberTransaction->user_id = Yii::app()->user->id;
          $memberTransaction->balance =
            $memberTransaction->member->accountBalance +
            $memberTransaction->amount;

          $member->state_id = Constants::MEMBER_NORMAL;
          $applicationForm->state_id = Constants::APPLICATION_REGISTERED;

          $dbTransaction = Yii::app()->db->beginTransaction();

          if ($memberTransaction->save() && $member->save() &&
            $applicationForm->save()
          ) {
            // {
              // $receiptNo = MhcPoll::pollNewReceiptNo();
              // if (!$receiptNo)
                // throw new CException('Could not get a valid receipt number.');
              // $memberTransaction->transaction_id = $receiptNo;
              // $memberTransaction->save();
            // }
            $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
              ClientAudit::AUDIT_DATA_PAYMENT_COLLECTION, $memberTransaction,
              "First payment collected!");
            $dbTransaction->commit();

            // MhcPoll::updateMember($member->id);
            // MhcPoll::updateTransaction($memberTransaction->transaction_id);
            Yii::app()->user->setFlash('success', 'First Payment amount ' .
              Helpers::currency($memberTransaction->amount, 'MVR') . ' has been'
              . ' credited to member account');
            $this->redirect(['members/printReceipt',
              'id' => $memberTransaction->transaction_id]);
          } else {
            $dbTransaction->rollback();
          }
        }
      }

      $this->render('regFee', [
        'formTitle' => 'Member First payment',
        'memberTransaction' => $memberTransaction,
      ]);
    }
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   *
   * @param integer $id the ID of the model to be loaded
   *
   * @return ApplicationForms the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = ApplicationFormsHelper::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, 'The requested page does not exist.');

    return $model;
  }

  /**
   * Performs the AJAX validation.
   *
   * @param ApplicationForms $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'application-forms-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
