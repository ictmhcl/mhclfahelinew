<?php

class UmraController extends Controller {

  public $layout = '//layouts/column2';

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
        'actions' => ['available','umraStatement', 'umraDetails',
                      'createUmraPilgrim'],
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

  public function actionCreateUmra() {
    $model = new UmraTrips();

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['UmraTrips'])) {
      $model->attributes = $_POST['UmraTrips'];
      $dbTransaction = Yii::app()->db->beginTransaction();
      $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
        ClientAudit::AUDIT_DATA_UMRA_TRIP, $model, 'Create Umra Trip ' .
        $model->name_english);
      if ($model->save()) {
        $dbTransaction->commit();
        Yii::app()->user->setFlash('success', 'Umra Trip ' .
          $model->name_english . ' has been created.');
        $this->redirect(['listUmraTrips']);
      } else {
        $dbTransaction->rollback();
        Yii::app()->user->setFlash('error', 'An error occurred. Please try later');
      }
    }

    $this->render('createUmraTrip', ['model' => $model]);
  }

  public function actionUpdateUmra($id) {
    $model = $this->loadModel($id);

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['UmraTrips'])) {
      $model->setAttributes($_POST['UmraTrips']);

      $dbTransaction = Yii::app()->db->beginTransaction();
      $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_EDIT,
        ClientAudit::AUDIT_DATA_UMRA_TRIP, $model, 'Updated Umra Details of
					 ' . $model->name_english);
      if ($model->save()) {
        $dbTransaction->commit();
        Yii::app()->user->setFlash('success', 'Umra Trip ' .
          $model->name_english . ' has been successfully updated.');
        $this->redirect(['listUmraTrips']);
      }
    }

    $this->render('updateUmraTrip', ['model' => $model]);
  }

  public function loadModel($id) {
    $model = UmraTrips::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, 'The requested page does not exist.');

    return $model;
  }

  public function actionListUmraTrips() {
    $umraTripsDP = new CActiveDataProvider('UmraTrips', [
      'pagination' => ['pageSize' => Helpers::config('pageSize')],
      'sort' => ['defaultOrder' => 'year desc, month desc', 'attributes' => [
        'name_english', 'month', 'year', 'price', 'tripTime' => ['asc' =>
          'year asc, month asc', 'desc' => 'year desc, month desc']
      ]]
    ]);
    $this->render('listUmraTrips', ['umraTripsDP' => $umraTripsDP]);
  }

  public function actionRegisteredUmras() {
    $personUmras = $this->person->umraPilgrims(['condition'=>'']);
  }

  public function actionAvailable() {
    $criteria = new CDbCriteria();
    $criteria->addCondition("departure_date > CURDATE()");
    $criteria->addCondition("STR_TO_DATE(CONCAT(`year`,' ',`month`,' ',1),
      '%Y %m %d') > CURDATE()",'OR');
    $exceptInternalCondition = new CDbCriteria();
    $exceptInternalCondition->addCondition("internal_only = 0");
    $criteria->mergeWith($exceptInternalCondition);
    //$criteria->order = '`year` asc,`month` asc';
    $availableDp = new CActiveDataProvider('UmraTrips', [
      'criteria' => $criteria,
      'sort' => ['defaultOrder' => '`year` asc, `month` asc','attributes' =>[
        'year', 'month', 'deadline_date', 'price', 'name_dhivehi',
        'name_english'
      ]]
    ]);

    $this->render('available',compact('availableDp'));


  }

  public function actionSearchPilgrim($q) {
    if (empty($q))
      $this->redirect(['listUmraTrips']);

    if (!(preg_match(Constants::ID_CARD_PATTERN, $q) ||
      preg_match(Constants::ID_CARD_PATTERN, "A" . trim($q)))
    )
    {
      Yii::app()->user->setFlash('error', 'Please enter a valid ID Card
      Number');
      $this->redirect(['listUmraTrips']);
    }

    if (!preg_match(Constants::ID_CARD_PATTERN, $q))
      $q = "A" . trim($q);

    $sql = "
      SELECT up.id, utr.name_english, utr.price,
        max(ut.transaction_time) as last_payment_date,
        sum(ut.amount) as paid
      FROM umra_pilgrims up
      LEFT JOIN
        (
          SELECT umra_pilgrim_id, amount, transaction_time
          FROM umra_transactions
          WHERE is_cancelled = 0
        ) ut ON up.id = ut.umra_pilgrim_id
      LEFT JOIN persons p on up.person_id = p.id
      JOIN umra_trips utr ON utr.id = up.umra_trip_id
      WHERE p.id_no = :idNo
      GROUP BY up.id, up.person_id, up.umra_trip_id, utr.price
    ";

    $pilgrimSummary = Yii::app()->db->createCommand($sql)->bindParam
    (':idNo',$q, PDO::PARAM_STR)->queryAll(true);

    if (empty($pilgrimSummary))
    {
      Yii::app()->user->setFlash('error', 'Cannot find the ID in current Umra
       Pilgrim lists');
      $this->redirect(['listUmraTrips']);
    }
    $pilgrimTrips = [];
    foreach($pilgrimSummary as $pilgrimTrip)
      $pilgrimTrips[] = (object) $pilgrimTrip;

//    CVarDumper::dump($pilgrimTrips,10,1);die;

    $dp = new CArrayDataProvider($pilgrimTrips,[
      'keyField' => 'id',
      'sort' => ['defaultOrder' => 'last_payment_date desc'],
      'pagination' => false
    ]);

    $this->render('listPilgrimTrips', ['dp'=> $dp]);

  }

  public function actionCreateUmraPilgrim($umraTripId) {
    $model = new UmraPilgrims();

    /** @var UmraTrips $trip */
    $trip = UmraTrips::model()->findByPk($umraTripId);
    if (empty($trip) || $trip->closed || $trip->internal_only) {
      throw new CHttpException(404);
    }
    $model->person_id = $this->person->id;
    $model->umra_trip_id = (int) $umraTripId;
    $model->current_island_id = $this->person->perm_address_island_id;
    $model->current_address_english = $this->person->perm_address_english;
    $model->current_address_dhivehi = $this->person->perm_address_dhivehi;
    $model->phone_number = $this->person->phone;
    $model->email_address = $this->person->email;
    $model->id_copy = $this->person->idCopy;

    $docsArray = ['application_form', 'mahram_document'];


    if (!empty($_POST['UmraPilgrims'])) {


      $model->setAttributes($_POST['UmraPilgrims']);
      $mahramId = $model->mahram_id;
      $model->umra_pilgrim_no = (int)(Yii::app()->db->createCommand("
			SELECT max(umra_pilgrim_no) FROM umra_pilgrims WHERE umra_trip_id=:tripId
			")->queryScalar([':tripId' => $model->umra_trip_id])) + 1;

      $dbTransaction = Yii::app()->db->beginTransaction();

      try {
        // Save uploaded files
        // Iterate file inputs recieved
        $modelClass = get_class($model);
        // foreach ($_FILES[$modelClass]['name'] as $fileAttribute => $fileName) {
        //   $error = $_FILES[$modelClass]['error'][$fileAttribute];

        //   // if file was not collected by the system
        //   if ($error > 0) {

        //     // if file was not provided by user
        //     if (empty($fileName)) {

        //       // add error if this is a required file
        //       $model->validate([$fileAttribute], false);
        //     } else { // if file was uploaded but rejected
        //       $model->addError($fileAttribute, $model->getAttributeLabel($fileAttribute) . ' (' . $_FILES[$modelClass]['name'][$fileAttribute] . ') was not accepted!'
        //         . ' File may be larger than server limit.');
        //     }
        //   } else { // file was collected by system
        //     // unique file name
        //     $fileName = uniqid() . '_' . $this->person->id_no . '_' . $fileName;

        //     $acceptFile = true; // by default
        //     // check if a file already exists, and if so get path & delete it;
        //     // do no accept file only if current file could be deleted!
        //     if (!empty($model[$fileAttribute]) && !Helpers::deleteUploadedFile($model[$fileAttribute])) {
        //       $acceptFile = false; // Do not accept file
        //       Yii::app()->user->setFlash('error', 'There has been an internal error! Could not replace existing file!');
        //     }

        //     if ($acceptFile) {
        //       $model[$fileAttribute] = $fileName;
        //       move_uploaded_file($_FILES[$modelClass]['tmp_name'][$fileAttribute], Yii::app()->params['uploadPath'] . $fileName);
        //     }
        //   }
        // }
        $model['application_form'] = 'not collected';

        if ($model->save()) {

            $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
              ClientAudit::AUDIT_DATA_UMRA_PILGRIM, $model, 'New Umra Pilgrim ' .
              $this->person->full_name_english . ' (' . $this->person->id_no . ') added to
            Umra Trip ' . $model->umraTrip->name_english, [$this->person]);
            $dbTransaction->commit();
            Yii::app()->user->setFlash('success', $this->person->full_name_dhivehi . '
              (' . $this->person->id_no . ') ގެ ފޯމް ޙައްޖު ކޯޕަރޭޝަނަށް
              ލިބިއްޖެއެވެ.' .
              $model->umraTrip->name_dhivehi . ' އަށް މިހާރު ފައިސާ
              ދެއްކިދާނެއެވެ.');
            $this->redirect(['umra/available']);
          }

          $dbTransaction->rollback();
      } catch (Exception $ex) {
        $dbTransaction->rollback();
        ErrorLog::exceptionLog($ex);
        Yii::app()->user->setFlash('error', 'There has been an error. Please try again later!');
      }
      $model->mahram_id = $mahramId;
    }

    $this->render('createUmraPilgrim', [
      'model' => $model,
      'trip' => $trip,
      'docsArray' => $docsArray

    ]);


  }

  public function actionListUmraPilgrims($umraTripId = null) {
    if (empty($umraTripId)) {
      $umraTripId = Yii::app()->db->createCommand("
				SELECT id
				FROM umra_trips
				WHERE `year` >= YEAR(CURDATE()) AND `month` >= MONTH(CURDATE())
				ORDER BY year asc, month asc
				LIMIT 1
			")->queryScalar();
    }
    $model = UmraTrips::model()->findByPk($umraTripId);
    if ($model === null) $this->redirect('listUmraTrips');

    $criteria = new CDbCriteria();
    $criteria->with = [
      'person', 'mahram', ['mahram.person' => ['alias' => 'mahramPerson']]
    ];
    $criteria->compare('t.umra_trip_id', $umraTripId);
    $umraTripPilgrimsDP = new CActiveDataProvider('UmraPilgrims', [
      'criteria' => $criteria,
      'sort' => ['defaultOrder' => 't.group_name asc', 'attributes' => [
        'person.full_name_english','phone_number', 'person.id_no',
        't.account_balance',
        't.full_payment_date_time', 't.group_name', 'mahramPerson.full_name_english'
      ]]
    ]);
    $this->render('listUmraPilgrims', [
      'umraTripPilgrimsDP' => $umraTripPilgrimsDP,
      'model' => $model
    ]);
  }

  public function actionUmraPayment($id) {
    /** @var UmraPilgrims $model */
    $model = UmraPilgrims::model()->findByPk($id);
    if ($model === null)
      $this->redirect(['listUmraTrips']);

    $transaction = new UmraTransactions();
    $transaction->umra_pilgrim_id = (int)$id;

    if (isset($_POST['UmraTransactions'])) {
      if (!isset($_POST['feeCollected'])) {
        Yii::app()->user->setFlash('error', ' You have to confirm collection by marking the checkbox marked "Payment Received".');
      } else {
        $transaction->setAttributes($_POST['UmraTransactions']);
        $transaction->umra_pilgrim_id = (int)$id;
        $transaction->transaction_time = date('Y-m-d H:i:s');
        $transaction->description_english = 'Umra payment for ' .
          $model->umraTrip->name_english;
        $transaction->description_dhivehi = $model->umraTrip->name_dhivehi .
          ' އަށް ފައިސާ ދެއްކުން';
        $transaction->user_id = Yii::app()->user->id;
        $transaction->balance = $model->account_balance +
          (int)$transaction->amount;

        $model->account_balance = (empty($model->account_balance)
            ? 0 : $model->account_balance) + (int)
          $transaction->amount;
        if ($model->account_balance >= $model->umraTrip->price)
          $model->full_payment_date_time = (new DateTime
          ($transaction->transaction_time));

        $dbTransaction = Yii::app()->db->beginTransaction();

        if ($transaction->save() && $model->save()) {
          {
            $receiptNo = MhcPoll::pollNewReceiptNo('umra',
              $transaction->umraPilgrim->umra_trip_id);
            if (!$receiptNo)
              throw new CException('Could not get a valid receipt number.');
            $transaction->transaction_id = $receiptNo;
            $transaction->save();
          }
          $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
            ClientAudit::AUDIT_DATA_UMRA_PAYMENT_COLLECTION, $transaction,
            "Umra payment collected!");
          $dbTransaction->commit();
          Yii::app()->user->setFlash('success', 'Payment amount ' .
            Helpers::currency($transaction->amount, 'MVR') . ' has been'
            . ' credited to account of ' .
            $model->person->full_name_english . ' towards umra of ' .
            $model->umraTrip->name_english);
          $this->redirect(['umraPaymentReceipt', 'id' =>
            $transaction->transaction_id]);
        } else {
          $dbTransaction->rollback();
        }
      }
    }

    $this->render('payment', [
      'model' => $model,
      'transaction' => $transaction
    ]);
  }

  public function actionUmraPaymentReceipt($id, $view = false) {
    /** @var UmraTransactions $transaction */
    $transaction = UmraTransactions::model()->findByPk($id);
    $this->layout = '//layouts/print';
    $this->render(($transaction->amount < 0 ? 'voucher' : 'receipt'), [
      'transaction' => $transaction,
      'view' => $view
    ]);
  }

  public function actionUmraDebit($id) {

    /** @var UmraPilgrims $pilgrim */
    $pilgrim = UmraPilgrims::model()->findByPk($id);
    if (is_null($pilgrim)) {
      Yii::app()->user->setFlash('error', 'Invalid Request!');
      $this->redirect(['umra/listUmraTrips']);
    }

    $umraTransaction = new UmraTransactions();

    if (isset($_POST['UmraTransactions'])) {
      if (!isset($_POST['debitGiven'])) {
        Yii::app()->user->setFlash('error', 'You have to confirm debit by marking the checkbox marked "Debit Given".');
      } else {
        $umraTransaction->setAttributes($_POST['UmraTransactions']);
        if ($umraTransaction->amount <= 0)
          $umraTransaction->addError('amount', $umraTransaction->getAttributeLabel('amount') . ' cannot be less than or equal to zero.');

        if ($umraTransaction->amount > $pilgrim->account_balance)
          $umraTransaction->addError('amount',
            $umraTransaction->getAttributeLabel('amount') . ' cannot be more than pilgrims account balance.');

//        $oldBal = $pilgrim->account_balance;

        $umraTransaction->amount = -$umraTransaction->amount;
        $umraTransaction->umra_pilgrim_id = (int)$id;
        $umraTransaction->transaction_time = date('Y-m-d H:i:s');
        $umraTransaction->description_english = 'Debit Amount';
        $umraTransaction->description_dhivehi =
          $pilgrim->umraTrip->name_dhivehi . ' ދިއުމަށް ދެއްކި ފައިސާ އަނބުރާ ރައްދު ކުރުން';
        $umraTransaction->user_id = Yii::app()->user->id;
        $umraTransaction->balance = $pilgrim->account_balance + $umraTransaction->amount;
        $pilgrim->account_balance += $umraTransaction->amount;
        $dbTransaction = Yii::app()->db->beginTransaction();
        if ($pilgrim->save() && $umraTransaction->save()) {
          {
            $receiptNo = MhcPoll::pollNewReceiptNo('umra',
              $umraTransaction->umraPilgrim->umra_trip_id);
            if (!$receiptNo)
              throw new CException('Could not get a valid receipt number.');
            $umraTransaction->transaction_id = $receiptNo;
            $umraTransaction->save();
          }
          $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
            ClientAudit::AUDIT_DATA_UMRA_PAYMENT_COLLECTION, $umraTransaction,
            "Account Debited");

          $dbTransaction->commit();
          Yii::app()->user->setFlash('success', 'Returned amount ' .
            Helpers::currency(-$umraTransaction->amount, 'MVR') . ' has been'
            . ' debited to member account');
          //TODO: Debit Voucher to be done
          $this->redirect(['umra/umraPaymentReceipt',
            'id' => $umraTransaction->transaction_id]);
        } else {
          $umraTransaction->setAttributes($_POST['UmraTransactions']);
          $dbTransaction->rollback();
        }
      }
    }

    $umraTransaction->umra_pilgrim_id = (int)$id;

    $this->render('umraDebit', ['transaction' => $umraTransaction]);
  }

  public function actionUmraDetails($id) {
    /** @var UmraTrips $trip */
    $trip = UmraTrips::model()->findByPk($id);
    if (empty($trip) || $trip->internal_only)
      throw new CHttpException(404);

    $this->render('umraDetails',compact('trip'));
  }


  public function actionUmraStatement($id) {

    /** @var UmraPilgrims $pilgrim */
    $pilgrim = UmraPilgrims::model()->findByPk($id);
    if ($pilgrim === null || $pilgrim->person_id <> Yii::app()->user->id)
      throw new CHttpException(403);

    $dataProvider = new CActiveDataProvider('UmraTransactions', [
      'criteria' => [
        'condition' => 'umra_pilgrim_id = ' . (int)$id,
        'order' => 'transaction_time asc',
      ],
      'pagination' => [
        'pageSize' => Helpers::config('pageSize'),
      ],
    ]);

    $this->render('umraStatement', [
      'dataProvider' => $dataProvider,
      'pilgrim' => $pilgrim
    ]);
  }

  public function actionViewTransactionHistory($id) {
    /** @var UmraTransactions $umraTransaction */
    $umraTransaction = UmraTransactions::model()->findByPk($id);
    if (is_null($umraTransaction)) {
      $this->redirect(['umra/listUmraTrips']);
    }

    $this->render('reviseUmraTransaction', [
      'umraTransaction' => $umraTransaction,
      'editsDataProvider' => ClientAudit::auditLogDataProvider($umraTransaction),
      'viewOnly' => true
    ]);

  }

  public function actionReviseUmraTransaction($id, $mode = null) {

    /** @var UmraTransactions $umraTransaction */
    $umraTransaction = UmraTransactions::model()->findByPk($id);
    if (is_null($umraTransaction)) {
      $this->redirect(['umra/listUmraTrips']);
    }

    $viewOnly = $umraTransaction->is_cancelled;
    if (!$viewOnly) {
      if ($mode == 'cancel') {
        $umraTransaction->is_cancelled = 1;
        $dbTransaction = Yii::app()->db->beginTransaction();

        // Update member transaction balances
        // get all transactions from member
        $umraTransactions = UmraTransactions::model()->findAll([
          'condition' => 'umra_pilgrim_id = ' . $umraTransaction->umra_pilgrim_id,
          'order' => 'transaction_time ASC',
        ]);
        $bal = 0;
        foreach ($umraTransactions as $uTrans) {
          if (!$uTrans->is_cancelled)
            $bal += $uTrans->amount;
          $uTrans->balance = $bal;
          $uTrans->save();
        }
        $pilgrim = $umraTransaction->umraPilgrim;
        $pilgrim->account_balance = $bal;
        if ($bal < $pilgrim->umraTrip->price)
          $pilgrim->full_payment_date_time = null;

        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_DELETE,
          ClientAudit::AUDIT_DATA_UMRA_PAYMENT_COLLECTION, $umraTransaction,
          'Transaction on ' . $umraTransaction->transaction_time .
          ' for amount ' . Helpers::currency($umraTransaction->amount) .
          ' has been cancelled. (Remarks: ' . $_GET["editRemarks"] . ')',
          [$pilgrim]);

        $umraTransaction->save();
        $pilgrim->save();

        $dbTransaction->commit();

        Yii::app()->user->setFlash('success', 'Receipt "' .
          Helpers::umraReceiptNumber($umraTransaction) . '" has been Cancelled!');
        $this->redirect(['umra/umraStatement',
          'id' => $umraTransaction->umra_pilgrim_id]);
      }

      if (isset($_POST['UmraTransactions'])) {
        if (strtotime($umraTransaction->transaction_time) ==
          strtotime($_POST['UmraTransactions']['transaction_time']) &&
          ($umraTransaction->transaction_medium_id ==
            $_POST['UmraTransactions']['transaction_medium_id'])
        ) {
          Yii::app()->user->setFlash('error', 'There was no change to update!');
          $this->refresh();
        }
        $umraTransaction->setAttributes($_POST['UmraTransactions']);
        $dbTransaction = Yii::app()->db->beginTransaction();
        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_EDIT,
          ClientAudit::AUDIT_DATA_UMRA_PAYMENT_COLLECTION, $umraTransaction,
          'Transaction time or medium updated. (Remarks: ' .
          $_POST["editRemarks"] . ')');
        // TODO: Need to update transaction balances and umraPilgrim account
        // balance after changes in transaction time (in case transaction
        // times switch between two or more transactions). This needs to be
        // set form Hajj Members and Non-members as well.
        if ($umraTransaction->save()) {
          $dbTransaction->commit();
          Yii::app()->user->setFlash('success', 'Umra Receipt "' .
            Helpers::umraReceiptNumber($umraTransaction) .
            '" has been revised!');
          $this->refresh();
        } else {
          $dbTransaction->rollback();
          Yii::app()->user->setFlash('error',
            'Reciept could not be revised. Please check and try again!');
        }
      }
    }

    $this->render('reviseUmraTransaction', [
      'umraTransaction' => $umraTransaction,
      'editsDataProvider' => ClientAudit::auditLogDataProvider($umraTransaction),
      'viewOnly' => $viewOnly
    ]);
  }

  public function actionViewPilgrim($id) {
    $pilgrim = UmraPilgrims::model()->findByPk($id);
    if (empty($pilgrim)) $this->redirect(['listUmraTrips']);
    $this->render('viewPilgrim', ['pilgrim' => $pilgrim]);
  }

  public function actionEditPilgrim($id) {
    /** @var UmraPilgrims $model */
    $model = UmraPilgrims::model()->findByPk($id);
    if (empty($model)) $this->redirect(['listUmraTrips']);

    $docsArray = ['application_form', 'id_copy', 'mahram_document'];

    if (!empty($_GET['get_id'])) {
      /** @var Persons $person */
      $person = Persons::model()->findByAttributes(['id_no' =>
        $_GET['get_id']]);
      if ($person->member) {
        $model->phone_number = $person->member->phone_number_1;
        $model->email_address = $person->member->email_address;
      }
      if (is_null($person))
        Yii::app()->user->setFlash('error', 'ID not found');
    }

    if (!empty($_POST['UmraPilgrims'])) {
      // check if person exists
      $person = Persons::model()->findByAttributes(['id_no' => $_POST['Persons']['id_no']]);
      if ($person === null) {
        $person = new Persons();
      } else {
        $model->person_id = $person->id;
        $personIsUpdated = true;
      }

      $person->setAttributes($_POST['Persons']);
      $model->setAttributes($_POST['UmraPilgrims']);
      $model->person = $person;
      if ($model->isNewRecord && empty($model->umra_pilgrim_no))
        $model->umra_pilgrim_no = (int)(Yii::app()->db->createCommand("
        SELECT max(umra_pilgrim_no)
        FROM umra_pilgrims
        WHERE umra_trip_id = :umraTripId
        ")->queryScalar([':umraTripId' => $model->umra_trip_id])) + 1;

      $model->group_name = ($model->group_name == "" ? $_POST['otherGroupName']
        : $model->group_name);

      $dbTransaction = Yii::app()->db->beginTransaction();

      $person->scenario = 'membership';

      try {
        // Save uploaded files
        // Iterate file inputs received
        $modelClass = get_class($model);
        foreach ($_FILES[$modelClass]['name'] as $fileAttribute => $fileName) {
          $error = $_FILES[$modelClass]['error'][$fileAttribute];

          // if file was not collected by the system
          if ($error > 0) {

            // if file was not provided by user
            if (empty($fileName)) {

              // add error if this is a required file
              $model->validate([$fileAttribute], false);
            } else { // if file was uploaded but rejected
              $model->addError($fileAttribute, $model->getAttributeLabel($fileAttribute) . ' (' . $_FILES[$modelClass]['name'][$fileAttribute] . ') was not accepted!'
                . ' File may be larger than server limit.');
            }
          } else { // file was collected by system
            // unique file name
            $fileName = uniqid() . '_' . $person->id_no . '_' . $fileName;

            $acceptFile = true; // by default
            // check if a file already exists, and if so get path & delete it;
            // do no accept file only if current file could be deleted!
            if (!empty($model[$fileAttribute]) && !Helpers::deleteUploadedFile($model[$fileAttribute])) {
              $acceptFile = false; // Do not accept file
              Yii::app()->user->setFlash('error', 'There has been an internal error! Could not replace existing file!');
            }

            if ($acceptFile) {
              $model[$fileAttribute] = $fileName;
              move_uploaded_file($_FILES[$modelClass]['tmp_name'][$fileAttribute], Yii::app()->params['uploadPath'] . $fileName);
            }
          }
        }

        $personSaved = $person->save();
        $model->person_id = $person->id;
        $umraPilgrimSaved = $model->save();

        if ($personSaved && $umraPilgrimSaved) {
          $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_EDIT,
            ClientAudit::AUDIT_DATA_UMRA_PILGRIM, $model, 'Umra Pilgrim ' .
            $person->full_name_english . ' (' . $person->id_no . ') updated in
					Umra Trip ' . $model->umraTrip->name_english, [$person]);
          $dbTransaction->commit();
          if (!empty($personIsUpdated) && $personIsUpdated == true)
            Yii::app()->user->setFlash('warning', 'Person record has been updated!');
          Yii::app()->user->setFlash('success', $person->full_name_english . '
						(' . $person->id_no . ') has been updated for ' .
            $model->umraTrip->name_english . '.');
          $this->redirect(['listUmraPilgrims', 'umraTripId' => $model->umra_trip_id]);
        }
        $dbTransaction->rollback();
//        }
      } catch (Exception $ex) {
        $dbTransaction->rollback();
        ErrorLog::exceptionLog($ex);
        Yii::app()->user->setFlash('error', 'There has been an error. Please try again later!');
      }
    }

    $this->render('editPilgrim', ['model' => $model]);
  }
  // Uncomment the following methods and override them if needed
  /*
  public function filters()
  {
    // return the filter configuration for this controller, e.g.:
    return array(
      'inlineFilterName',
      array(
        'class'=>'path.to.FilterClass',
        'propertyName'=>'propertyValue',
      ),
    );
  }

  public function actions()
  {
    // return external action classes, e.g.:
    return array(
      'action1'=>'path.to.ActionClass',
      'action2'=>array(
        'class'=>'path.to.AnotherActionClass',
        'propertyName'=>'propertyValue',
      ),
    );
  }
  */
}