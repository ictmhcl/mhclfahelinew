<?php

class PayController extends Controller {

  public  $layout         = '//layouts/column2';
  private $responses      = null;
  private $reasons        = null;
  private $paymentDetails = null;
  private $paymentParams  = null;

  public function init() {
    parent::init();
    $this->responses = [1 => 'Authorized', 2 => 'Declined', 3 => 'Error'];
    $this->reasons = [
      1 => ['Transaction is successful', 'Merchant to display the confirmation page to cardholder'],
      101 => ['Invalid field passed to 3D Secure MPI', 'Merchant needs to check error description to find out what is wrong with the field. Authorization/Authentication not carried out'],
      201 => ['Invalid ACS response format. Transaction is aborted', 'Retry the transaction. If error persists, contact issuing bank'],
      202 => ['Cardholder failed the 3D authentication, password entered by cardholder is incorrect and transaction is aborted', 'Merchant to display error page to cardholder'],
      203 => ['3D PaRes has invalid signature. Transaction is aborted','Retry the transaction. If error persists, contact issuing bank'],
      300 => ['Transaction not approved','Transaction has failed authorization, e.g. due to insufficient credit, invalid card number, etc. The actual response code provided by acquiring host can be found via the View Transaction History web page available to merchants'],
      301 => ['Record not found','Merchant/User has submitted a transaction with invalid purchase ID. Merchant/User tried to reverse a previously declined transaction'],
      302 => ['Transaction not allowed','Purchase ID not unique due to mismatched card number and/or transaction amount. System unable to process reversal due to transaction has been settled. System unable to process reversal due to transaction type is CAPS. System unable to process previously voided transaction'],
      303 => ['Invalid Merchant ID','Not a valid merchant account'],
      304 => ['Transaction blocked by error 901','Merchant to report error to acquiring bank'],
      900 => ['3D Transaction timeout','Timeout of 3D transaction due to late response from Issuer ACS, after the predefined 3D timeout set in the application'],
      901 => ['System Error','System unable to complete transaction. Merchant to report error to acquiring bank'],
      902 => ['Time out','Issuing/acquiring host timeout, transaction is not approved'],
    ];
    $this->paymentDetails = Yii::app()->session->get('paymentDetails');
    $this->paymentParams = $GLOBALS['cfg']['bml_mpg_settings'];

  }

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
        'actions' => [''],
        'users' => ['*'],
      ],
      ['allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => ['process', 'testPayment', 'payNow'],
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

  private function setupFixtures() {
    $this->person = Persons::model()->findByPk(1);

    return [
      [
        'payment_type_id' => '2',
        'payload' => '{"umra_pilgrim_id":"176"}',
        'amount' => '34965',
      ],
      [
        'payment_type_id' => '2',
        'payload' => '{"umra_pilgrim_id":"180"}',
        'amount' => '2000',
      ],
      [
        'payment_type_id' => '2',
        'payload' => '{"umra_pilgrim_id":"180"}',
        'amount' => '5000',
      ],
      [
        'payment_type_id' => '3',
        'payload' => '{"ageega":{"ageega_reason_id":"2","paid_amount":"0.00","operation_log_id":1,"ageega_form":"589113a5a1ed5_A234295_DepartureCards (3).pdf","person_id":"7592","phone_number":"9722525","id":null,"full_payment_date_time":null},"sheepQty":"2","children":[]}',
        'amount' => '3394',
      ],
      [
        'payment_type_id' => '3',
        'payload' => '{"ageega":{"ageega_reason_id":"1","paid_amount":"0.00","operation_log_id":1,"ageega_form":"5891144868ab5_A234295_DepartureCards (3).pdf","person_id":"7592","phone_number":"9722525","id":null,"full_payment_date_time":null},"sheepQty":0,"children":[{"operation_log_id":1,"full_name_english":"Hassan Idrees Ali","full_name_dhivehi":"\\u0799\\u07a6\\u0790\\u07a6\\u0782\\u07b0 \\u0787\\u07a8\\u078b\\u07b0\\u0783\\u07a9\\u0790\\u07b0 \\u07a2\\u07a6\\u078d\\u07a9","gender_id":"1","birth_certificate_no":"G-2331\\/2016","sheep_qty":"2","id":null,"ageega_id":null,"full_name_arabic":null},{"operation_log_id":1,"full_name_english":"Fathimath Ahmed Didi","full_name_dhivehi":"\\u078a\\u07a7\\u07a0\\u07a8\\u0789\\u07a6\\u078c\\u07aa \\u0787\\u07a6\\u0799\\u07b0\\u0789\\u07a6\\u078b\\u07aa \\u078b\\u07a9\\u078b\\u07a9","gender_id":"2","birth_certificate_no":"G-2344\\/2016","sheep_qty":"1","id":null,"ageega_id":null,"full_name_arabic":null}]}',
        'amount' => '5091',
      ],
      [
        'payment_type_id' => '1',
        'payload' => '',
        'amount' => '52473.75',
      ]
    ];
  }


  public function actionTestPayment() {
	  

    $fixtures = $this->setupFixtures();
    $paymentDetails = $fixtures[rand(0, 5)];

    Yii::app()->session->add('paymentDetails', $paymentDetails);
    $payment = new BmlMpgLogs();
    $payment->setAttributes([
      'payment_type_id' => (int)$paymentDetails['payment_type_id'],
      'person_id' => $this->person->id, 'payload' => $paymentDetails['payload'],
      'amount' => $paymentDetails['amount'],
      'issued_date_time' => Yii::app()->params['dateTime'],
    ]);

    if (!$payment->save()) {
      throw new CHttpException(500);
    }
    Yii::app()->session->add('paymentDetails', $paymentDetails
      + ['id' => $payment->id]);

    $this->redirect(['pay/process']);

  }

  public function actionPayNow() {
	  


    Yii::app()->user->returnUrl = Yii::app()->request->urlReferrer;


    // payment type & amount is required
    if (empty($_POST['payment_type_id']) || empty($_POST['paymentAmount'])
      || empty($_POST['bankAmount'])
      || empty($_POST['creditAmount']))
      return;

    $paymentTypeId = $_POST['payment_type_id'];
    $amount = $_POST['creditAmount'];


    // validate payload info for Umra / Ageega
    if (in_array($paymentTypeId, [
      Constants::ONLINE_PAYMENT_UMRA, Constants::ONLINE_PAYMENT_AGEEGA
    ])) {
      //payload is required for Umra & Ageega
      if (empty($_POST['payload']))
        return;

      $payload = CJSON::decode($_POST['payload']);
      if ($paymentTypeId == Constants::ONLINE_PAYMENT_UMRA) {
        if (empty($payload['umra_pilgrim_id']))
          return;
      } elseif ($paymentTypeId == Constants::ONLINE_PAYMENT_AGEEGA) {
        if (empty($payload['ageega']))
          return;
      }
    } else
      $payload = "Hajj Payment";


    $payment = new BmlMpgLogs();
    $payment->setAttributes([
      'payment_type_id' => (int)$paymentTypeId,
      'person_id' => $this->person->id,
      'payload' => CJSON::encode($payload),
      'amount' => $amount,
      'issued_date_time' => Yii::app()->params['dateTime'],
    ]);
    if (!$payment->save())
      throw new CHttpException(500);
    Yii::app()->session->add('paymentDetails', $_POST + ['id' => $payment->id]);

    $this->redirect(['pay/process']);

  }
  
  public function actionProcess() {
		
		// Check for bml response
		if (!empty($_GET['state'])) {
			
		  
		  /*echo "Here goes BML response<br>";
		  echo "<pre>";
		  print_r($_GET);
		  echo "</pre>";
		  echo "<br>Code Terminated";
		  exit;*/
		  
		  
		  
		  // redundant block
			#region Validate if response is in expected format
			if (empty($_GET['state'])){
				throw new CHttpException(500);
			}
		  
			
			$bmlResponse = (object) $_GET;
			
			$bmlMpgLog = BmlMpgLogs::model()->findByPk($this->paymentDetails['id']);
			if (empty($bmlMpgLog)|| !empty($bmlMpgLog->response_date_time)){
				throw new CHttpException(500);
			}
		     
			
			// Mapping new BML status
			if ( $bmlResponse->state == "CONFIRMED" ) {
				$bmlResponse->ResponseCode = 1;
			}
			else if ( $bmlResponse->state == "CANCELLED" ) {
				$bmlResponse->ResponseCode = 2;
			}
			else {
				$bmlResponse->ResponseCode = 3;
			}
			
			
			$bmlResponse->ReasonCode = ""; 		// Not recieved from BML
			$bmlResponse->ReasonCodeDesc = ""; 	// Not recieved from BML
			$bmlResponse->PaddedCardNo = ""; 	// Not recieved from BML
			$bmlResponse->AuthCode = ""; 		// Not recieved from BML
			
			$newReferenceNumber = $bmlResponse->transactionId;
			
			
			
			#region Update Payment Record
			  $bmlMpgLog->mpg_response_code 		= $bmlResponse->ResponseCode;
			  $bmlMpgLog->mpg_reason_code 			= $bmlResponse->ReasonCode;
			  $bmlMpgLog->mpg_reason_description 	= $bmlResponse->ReasonCodeDesc;
			  $bmlMpgLog->response_date_time 		= Yii::app()->params['dateTime'];
			  // APPROVED
			  $bmlMpgLog->save();

			  switch ($bmlResponse->ResponseCode) {
				case 1: // APPROVED
				  $bmlMpgLog->mpg_signature 			= $bmlResponse->signature;
				  $bmlMpgLog->mpg_card_number 			= $bmlResponse->PaddedCardNo;
				  $bmlMpgLog->mpg_reference_number 		= $newReferenceNumber;
				  $bmlMpgLog->mpg_authorization_code 	= $bmlResponse->AuthCode;
				  $bmlMpgLog->save();
				  $this->paymentApproved();
				  Yii::app()->session->remove('paymentDetails');

				  break;
				case 2: // DECLINED (NEW BML STATUS IS CANCELLED)
				  Yii::app()->user->setFlash('error', H::t('hajj','declinedMsg'));
				  break;
				case 3: // ERROR
				  Yii::app()->user->setFlash('error', H::t('hajj','gatewayError'));
				  break;
			  }
			  $this->redirect(Yii::app()->user->returnUrl);

			  #endregion

			  Yii::app()->end();
		  
		  
		  
		}
	  
		// Else
		else {
			
			#region setup MPG form post params
			$this->paymentParams['orderId'] = $this->paymentDetails['id'];
			$this->paymentParams['responseUrl'] = Yii::app()->createAbsoluteUrl($this->paymentParams['responseUrl']);
			$this->paymentParams['amount'] = $this->formatPaymentAmount($this->paymentDetails['creditAmount']);

			#region setup signature
			$strToHash = $this->paymentParams['password'] . $this->paymentParams['merchantId']
			. $this->paymentParams['acquirerId'] . $this->paymentParams['orderId']
			. $this->paymentParams['amount']
			. $this->paymentParams['purchaseCurrency'];
			unset($this->paymentParams['password']);
			// $this->paymentParams['signature'] = base64_encode(sha1($strToHash, true));

			$localId = rand(1000,9999); // Change to invoice or billing number (must be unique )
			$amount = $this->paymentParams['amount'];
			$amount = ltrim($amount, 0); // Removed leading zeros
			$currency = $this->paymentParams['purchaseCurrency'];
			$apiKey = $this->paymentParams['apiKey'];
			$this->paymentParams['localId']  = $localId;
			$this->paymentParams['customerRef']  = $localId;


			// Generate a signature
			$this->paymentParams['signature'] = sha1("id=$localId&amount=$amount&currency=$currency&apiKey=$apiKey");
			#endregion
			$params = (object)$this->paymentParams;
			#endregion

			#region Create Form and send to browser to be automatically submit

			$headers = [
				"Authorization:$apiKey",
				'Accept: application/json',
				'Content-Type: application/json'
			];

			$postItems = [
				"localId" => $localId,
				"customerReference" => $params->customerRef,
				"signature" => $params->signature,
				"amount" => $amount,
				"currency" => $params->purchaseCurrency,
				"provider" => $params->provider,
				"appVersion" => $params->appVersion,
				"apiVersion" => $params->apiVersion,
				"deviceId" => NULL,
				"signMethod" => $params->signatureMethod,
				"redirectUrl" => $params->responseUrl
			];

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $params->url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postItems));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$serverResponse = curl_exec($ch);

			curl_close($ch);

			if ( empty($serverResponse) ) {
				throw new CHttpException(500, "No response from BML");
			}

			$serverResponse = json_decode($serverResponse, true);

			if( isset($serverResponse['url']) ) {
				header("Location: " . $serverResponse['url']);
				exit;
			}
			else {
				throw new CHttpException(404, "Unable to find url");
			}
			
			
			Yii::app()->end();
			
			
		}
		  
	  
  }

  public function actionProcess__() {

    #region Validate if a payment is in progress
    // Payment Process needs the Payment Details to be present (see $this->init)
    if (!$this->paymentDetails)
      $this->redirect(['/']);

    #endregion


	//CVarDumper::dump([$_POST, $_GET, Yii::app()->request->urlReferrer],10,1); die;
	
	/*echo "<pre>";
	print_r($_POST);
	
	print_r($_GET);
	echo "</pre>";
	exit;/*

    #region Handle BML response on payment request
    /*if (!empty($_POST)
      && Yii::app()->request->urlReferrer == 'https://egateway.bankofmaldives.com.mv/bmlmpiprod/threed/MPITermURL'
    ) {*/
	if (!empty($_GET)) {
		
		

      #region Validate if response is in expected format
      if (empty($_GET['state']))
        throw new CHttpException(500);
      #endregion

      #region Initialize Reponse & Pending Payment Record
      $bmlResponse = (object) $_GET;
    /**
     * This is for Testing Only
     */
//    if (1) {
//      // dummy Response
//      $bmlResponse = new stdClass();
//      $bmlResponse->ResponseCode = rand(1,3);
//      $bmlResponse->ReasonCode = 1;
//      $bmlResponse->ReasonCodeDesc = 'some desc';
//      $bmlResponse->Signature = 'xxxxx';
//      $bmlResponse->PaddedCardNo = '4213xxxxxxxx7509';
//      $bmlResponse->ReferenceNo = '';
//      $bmlResponse->AuthCode = 'asdfasfd234';


      /** @var BmlMpgLogs $bmlMpgLog */

      $bmlMpgLog = BmlMpgLogs::model()->findByPk($this->paymentDetails['id']);
      if (empty($bmlMpgLog))
        throw new CHttpException(500);
      #endregion

      #region Update Payment Record
      $bmlMpgLog->mpg_response_code = $bmlResponse->ReasonCode;
      $bmlMpgLog->mpg_reason_code = $bmlResponse->ReasonCode;
      $bmlMpgLog->mpg_reason_description = $bmlResponse->ReasonCodeDesc;
      $bmlMpgLog->response_date_time = Yii::app()->params['dateTime'];
      // APPROVED
      $bmlMpgLog->save();

      switch ($bmlResponse->state) {
        case "APPROVED": // APPROVED
          $bmlMpgLog->mpg_signature = $bmlResponse->Signature;
          $bmlMpgLog->mpg_card_number = $bmlResponse->PaddedCardNo;
          $bmlMpgLog->mpg_reference_number = $bmlResponse->ReferenceNo;
          $bmlMpgLog->mpg_authorization_code = $bmlResponse->AuthCode;
          $bmlMpgLog->save();
          $this->paymentApproved();
          Yii::app()->session->remove('paymentDetails');

          break;
        case "DECLINED": // DECLINED
		case "CANCELLED": // CANCELLED
          Yii::app()->user->setFlash('error', H::t('hajj','declinedMsg'));
          break;
        case 3: // ERROR
          Yii::app()->user->setFlash('error', H::t('hajj','gatewayError'));
          break;
      }
      $this->redirect(Yii::app()->user->returnUrl);

      #endregion

      Yii::app()->end();
    }
    #endregion

    #region Handle Payment Request Calls
    else {

      #region setup MPG form post params
      $this->paymentParams['orderId'] = $this->paymentDetails['id'];
      $this->paymentParams['responseUrl'] =
        Yii::app()->createAbsoluteUrl($this->paymentParams['responseUrl']);
      $this->paymentParams['amount'] =
        $this->formatPaymentAmount($this->paymentDetails['creditAmount']);

      #region setup signature
      $strToHash =
        $this->paymentParams['password'] . $this->paymentParams['merchantId']
        . $this->paymentParams['acquirerId'] . $this->paymentParams['orderId']
        . $this->paymentParams['amount']
        . $this->paymentParams['purchaseCurrency'];
      unset($this->paymentParams['password']);
      // $this->paymentParams['signature'] = base64_encode(sha1($strToHash, true));

        $localId = rand(1000,9999); // Change to invoice or billing number (must be unique )
        $amount = $this->paymentParams['amount'];
		$amount = ltrim($amount, 0); // Removed leading zeros
        $currency = $this->paymentParams['purchaseCurrency'];
        $apiKey = $this->paymentParams['apiKey'];
        $this->paymentParams['localId']  = $localId;
        $this->paymentParams['customerRef']  = $localId;


        // Generate a signature
        $this->paymentParams['signature'] = sha1("id=$localId&amount=$amount&currency=$currency&apiKey=$apiKey");
      #endregion
      $params = (object)$this->paymentParams;
      #endregion

      #region Create Form and send to browser to be automatically submit


        $headers = [
            "Authorization:$apiKey",
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $postItems = [
            "localId" => $localId,
            "customerReference" => $params->customerRef,
            "signature" => $params->signature,
			"amount" => $amount,
            "currency" => $params->purchaseCurrency,
            "provider" => $params->provider,
            "appVersion" => $params->appVersion,
            "apiVersion" => $params->apiVersion,
            "deviceId" => NULL,
            "signMethod" => $params->signatureMethod,
            "redirectUrl" => $params->responseUrl
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $params->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postItems));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverResponse = curl_exec($ch);

        curl_close($ch);

        if ( empty($serverResponse) ) {
            throw new \yii\db\Exception("No response from BML");
        }

        $serverResponse = json_decode($serverResponse, true);

        if( isset($serverResponse['url']) ) {
            header("Location: " . $serverResponse['url']);
            exit;
        }
		else {
			echo "Unable to find url";
		}


//      $response = <<<EOT
//      <html>
//      <head>
//        <meta name = "viewport" content = "width=device-width, initial-scale=1.0, maximum-scale=1.0" />
//        <script language = "javascript">
//          this.history.forward(1);
//        </script>
//      </head>
//      <body onload = "document.form1.submit()" style = "margin:0;">
//        <form
//          name = "form1"
//          method = "post"
//          action = "$params->url">
//          <input name = "customerReference" type = "hidden" value = "$params->customerRef" >
//          <input name = "localId" type = "hidden" value = "$params->localId" >
//          <input name = "provider" type = "hidden" value = "$params->provider" >
//          <input name = "deviceId" type = "hidden" value = "NULL" >
//          <input name = "redirectUrl" type = "hidden" value = "" >
//          <!--<input name = "Version" type = "hidden" value = "$params->version" >-->
//          <!--<input name = "MerID" type = "hidden" value = "$params->merchantId" >-->
//         <!-- <input name = "AcqID" type = "hidden" value = "$params->acquirerId" >-->
//          <!--<input name = "MerRespURL" type = "hidden" value = "$params->responseUrl" >-->
//          <input name = "currency" type = "hidden" value = "$params->purchaseCurrency" >
//          <!--<input name = "PurchaseCurrencyExponent" type = "hidden" value = "2" >-->
//          <!--<input name = "OrderID" type = "hidden" value = "$params->orderId" >-->
//          <input name = "signMode" type = "hidden" value = "$params->signatureMethod" >
//          <input name = "amount" type = "hidden" value = "$params->amount" >
//          <input name = "signature" type = "hidden" value = "$params->signature" >
//          <div style = "height:100%; width:100%; border-top:10px solid green; text-align:center;font: 12px 'Open Sans',Helvetica,Arial;">
//            <p Style = "color:#D81E05; font-size:22px; margin: 0 auto"> ... ... ... </p>
//          </div >
//        </form>
//      </body >
//      </html >
//EOT;
//      echo $response;
      #endregion
      Yii::app()->end();

    }
    #endregion


  }

  private function formatPaymentAmount($amount) {
    $amount = doubleval($amount);

    return str_pad((int)($amount * 100), 12, '0', STR_PAD_LEFT);
  }

  private function paymentApproved() {
    // Create relevant Transaction Records as
    $paymentTypeId = $this->paymentDetails['payment_type_id'];
    switch ($paymentTypeId) {
      case (Constants::ONLINE_PAYMENT_HAJJ):
        $this->approvedHajjPayment();
        break;
      case (Constants::ONLINE_PAYMENT_UMRA):
        $this->approvedUmraPayment();
        break;
      case (Constants::ONLINE_PAYMENT_AGEEGA):
        $this->approvedAgeegaPayment();
        break;

    };
  }

  private function approvedHajjPayment() {
    $transaction = new MemberTransactions();
    $transaction->amount = $this->paymentDetails['creditAmount'];
    $transaction->user_id = 1;
    $transaction->balance = $this->person->member->accountBalance +
      $transaction->amount;
    $transaction->transaction_time = Yii::app()->params['dateTime'];
    $transaction->member_id = $this->person->member->id;
    $transaction->transaction_type_id = Constants::TRANSACTION_TYPE_DEPOSIT;
    $transaction->description_english = 'Online Deposits to Hajj account';
    $transaction->description_dhivehi = 'ޙައްޖު އެކައުންޓަށް އޮންލައިންކޮށް ދެއްކި ފައިސާ';
    $transaction->transaction_medium_id = Constants::TRANSACTION_MEDIUM_ONLINE;
    $transaction->is_cancelled = 0;
    $dbTrans = Yii::app()->db->beginTransaction();
    $dbTrans->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
      ClientAudit::AUDIT_DATA_PAYMENT_COLLECTION,$transaction, 'Online
      Payment');
    try {
      if ($transaction->save())
      {

        $msg = ($transaction->balance >= Constants::MATURITY_VALUE
          && $transaction->balance - $transaction->amount
          < Constants::MATURITY_VALUE)
          ? H::t('hajj','slotMsg') : "";
        Yii::app()->user->setFlash('success',
          H::t('hajj','paymentSuccess {amt} {msg}', [
          'amt' => Helpers::currency($transaction->amount),
          'msg' => $msg
        ]));

        $dbTrans->commit();
      } else {
        $dbTrans->rollback();
        throw new CHttpException(500);
      }
    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
    }
    $this->redirect(Yii::app()->user->returnUrl);
  }

  private function approvedUmraPayment() {
    $details = (object)CJSON::decode($this->paymentDetails['payload']);

    /** @var UmraPilgrims $model */
    $model = UmraPilgrims::model()->findByPk($details->umra_pilgrim_id);
    if ($model === null)
      $this->redirect(['listUmraTrips']);

    $transaction = new UmraTransactions();
    $transaction->umra_pilgrim_id = $model->id;
    $transaction->amount = $this->paymentDetails['creditAmount'];
    $transaction->transaction_time = Yii::app()->params['dateTime'];
    $transaction->transaction_medium_id = Constants::TRANSACTION_MEDIUM_ONLINE;
    $transaction->transaction_time = Yii::app()->params['dateTime'];
    $transaction->description_english = 'Online Payment for ' . $model->umraTrip->name_english;
    $transaction->description_dhivehi = $model->umraTrip->name_dhivehi . ' އަށް އޮންލައިންކޮށް ދެއްކި ފައިސާ';
    $transaction->user_id = 1;
    $transaction->balance = $model->account_balance + (int)$transaction->amount;

    $model->account_balance =
      (empty($model->account_balance) ? 0 : $model->account_balance)
      + (int)$transaction->amount;
    if ($model->account_balance >= ($model->umraTrip->price - (!empty
        ($model->umraTripDiscount)
          ? $model->umraTripDiscount->discount_amount : 0))
    )
      $model->full_payment_date_time = $transaction->transaction_time;
    else
      $model->full_payment_date_time = null;

    $dbTransaction = Yii::app()->db->beginTransaction();
    try {
      if ($transaction->save() && $model->save()) {
        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
          ClientAudit::AUDIT_DATA_UMRA_PAYMENT_COLLECTION, $transaction,
          "Umra online payment collected for " .
          $this->person->full_name_english);
        $dbTransaction->commit();
        Yii::app()->user->setFlash('success', H::t('hajj', 'paymentSuccess {amt} {msg}', [
          'amt' => Helpers::currency($transaction->amount),
          'msg' => ''
        ]));
      }
      else {
        $dbTransaction->rollback();
      }
    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
    }
    $this->redirect(Yii::app()->user->returnUrl);
  }

  private function approvedAgeegaPayment() {
    $details = (object) CJSON::decode($this->paymentDetails['payload']);
    $ageega = new Ageega();
    $ageega->setAttributes($details->ageega);
    $ageega->paid_amount = $this->paymentDetails['creditAmount'];
    $ageega->full_payment_date_time = Yii::app()->params['dateTime'];
    $dbTrans = Yii::app()->db->beginTransaction();

    try {
      $savingError = false;
      if ($ageega->save()) {
        $ageegaTransaction = new AgeegaTransactions();
        $ageegaTransaction->ageega_id = $ageega->id;
        $ageegaTransaction->setAttributes([
          'transaction_time' => Yii::app()->params['dateTime'],
          'description_english' => 'Online Ageega Payment',
          'description_dhivehi' => 'ކަތިލުމަށް އޮންލައިންކޮށް ދެއްކި ފައިސާ',
          'transaction_medium_id' => Constants::TRANSACTION_MEDIUM_ONLINE,
          'amount' => $this->paymentDetails['creditAmount'],
          'balance' => $this->paymentDetails['creditAmount'],
          'user_id' => 1,
          'is_cancelled' => 0
        ]);
        if (!$ageegaTransaction->save())
          $savingError = true;
        $childModels = [];
        if ($ageega->ageega_reason_id ==
          Constants::AGEEGA_REASON_CHILDREN_NAMING) {
          foreach($details->children as $child) {
            $childModel = new AgeegaChildren();
            $childModel->setAttributes($child);
            $childModel->ageega_id = $ageega->id;
            if (!$childModel->save())
              $savingError = true;
            $childModels[] = $childModel;
          }
        } else {
          $childModel = new AgeegaChildren();
          $childModel->full_name_english = $childModel->full_name_dhivehi = '-';
          $childModel->full_name_arabic = $childModel->birth_certificate_no = '-';
          $childModel->gender_id = Constants::GENDER_MALE;
          $childModel->sheep_qty = $details->sheepQty;
          $childModel->ageega_id = $ageega->id;
          if (!$childModel->save())
            $savingError = true;
          $childModels[] = $childModel;
        }
        if (!$savingError) {
          $dbTrans->doAudit(ClientAudit::AUDIT_ACTION_CREATE,
            ClientAudit::AUDIT_DATA_AGEEGA_TRANSACTION, $ageegaTransaction,
            'Ageega Online Payment', [$ageega] + $childModels);
          $dbTrans->commit();
          if (Yii::app()->session->contains('ageegaForm'))
            Yii::app()->session->remove('ageegaForm');
          Yii::app()->user->setFlash('success', H::t('hajj', 'paymentSuccess {amt} {msg}', [
            'amt' => Helpers::currency($ageegaTransaction->amount),
            'msg' => ''
          ]));
        }
      }
    } catch (CException $ex) {
      $dbTrans->rollback();
      ErrorLog::exceptionLog($ex);
    }
    $this->redirect(['ageega/list']);


  }

}
