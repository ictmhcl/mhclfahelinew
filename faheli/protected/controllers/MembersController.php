<?php

class MembersController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to
   *      '//layouts/column2', meaning using two-column layout. See
   *      'protected/views/layouts/column2.php'.
   */
  const MAX_MATURITY_COMPARE = 1000000;

  public $layout = '//layouts/column2';
  public $defaultAction = 'statement';

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
        'actions' => ['debit', 'viewTransactionHistory', 'printMemberAudit'],
        'users' => ['dev'],
      ],
      ['allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array_merge(Helpers::perms(), ['']),
        'users' => ['@'],
      ],
      ['allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => ['statement'],
        'users' => ['@'],
      ],
      ['deny', // deny all users
        'users' => ['*'],
      ],
    ];
  }


  /**
   * Displays Member Statement
   *
   * @param $id
   *
   * @throws CHttpException
   */
  public function actionStatement() {

    $this->layout = '//layouts/column1';
    if (empty($this->person->member)) {
      $this->redirect(['registration/index']);
    }
    //region Create Member Transactions Data Provider
    $dataProvider = new CActiveDataProvider('MemberTransactions', [
      'criteria' => [
        'condition' => 'is_cancelled =0 and member_id = ' . (int)$this->person->member->id,
        'order' => 'transaction_time asc, transaction_id asc',
      ],
      'pagination' => false,
    ]);
    //endregion

    //region Display Statement View
    $this->render('statement', [
      'dataProvider' => $dataProvider,
    ]);
    //endregion
  }

  /**
   * Export a Members Statement in CSV
   * @param $id
   *
   * @throws CException
   * @throws CHttpException
   */
  public function actionStatementExcel($id) {
    $model = $this->loadModel($id);

    $criteria = new CDbCriteria();
    $criteria->condition = 'member_id = ' . (int)$id;
    $criteria->order = 'transaction_time asc, transaction_id asc';

    $sql = "Select DATE_FORMAT(a.transaction_time,'%d %b %y') as 'Date', "
      . "a.description_english as 'Transaction Details', b.name_english as 'Mode', "
      . "if(a.amount > 0, NULL, -a.amount) as 'Debit', if(a.amount > 0, a.amount, NULL) as 'Credit', "
      . "a.balance FROM member_transactions a LEFT JOIN z_transaction_mediums b ON a.transaction_medium_id = b.id "
      . "WHERE a.member_id = " . $model->id . " "
      . "ORDER BY a.transaction_time asc, a.transaction_id asc";

    $rawData = Yii::app()->db->createCommand($sql)->queryAll();

    array_unshift($rawData, [
      'Member Account Statement'
    ], [
      ''
    ], [
      'Member Name: ' . $model->person->full_name_english
    ], [
      'MHC Number: ' . $model->MHC_ID
    ], [
      'ID Card Number: ' . $model->person->id_no
    ], [
      'Permanent Address: ' . $model->person->PermAddressText
    ], [
      'Statement Date: ' . date('d F Y')
    ], [
      ''
    ], [
      'Date', 'Transaction Details', 'Mode', 'Debit', 'Credit', 'Balance'
    ]);

    $title = $model->person->id_no . "_statement_" . date('Ymd-hs') . ".xls";

    Yii::import('ext.csv.CSVFileDownload');
    $csvFile = new CSVFileDownload;
    $csvFile->generateCSV($title, $rawData);
    //$this->twoDArrayToGoogleDataTable($rawArray, isset($_POST['switchAxes'])));
  }

  public function actionPrintReceipt($id, $view = false, $viewAudit = true) {
    $memberTransaction = MemberTransactions::model()->findByPk($id);
    $this->layout = '//layouts/print';
    $this->render(($memberTransaction->amount < 0 ? 'voucher' : 'receipt'), [
      'memberTransaction' => $memberTransaction,
      'model' => $memberTransaction->member,
      'view' => $view,
      'viewAudit' => $viewAudit
    ]);
  }

  public function actionPrintMemberAudit($id, $view = false) {
    $this->layout = "//layouts/print";

    $firstPayment = MemberTransactions::model()->find([
      'condition' => 'member_id = :memberId AND is_cancelled = 0 AND amount > 0',
      'params' => [':memberId'=> $id],
      'order' => 'transaction_time asc',
      'limit' => 1
    ]);
    if (empty($firstPayment)) {
      Yii::app()->user->setFlash('error', 'Could not find!');
      $this->redirect(['/']);
    }

    $this->render('memberAudit', [
      'transaction'=> $firstPayment,
      'view' => $view]);
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   *
   * @param integer $id the ID of the model to be loaded
   *
   * @return Members the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Members::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, 'The requested page does not exist.');

    return $model;
  }

  /**
   * Performs the AJAX validation.
   *
   * @param Members $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'members-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
