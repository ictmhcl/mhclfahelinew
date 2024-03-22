<?php

class UsersController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to
   *      '//layouts/column2', meaning using two-column layout. See
   *      'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column2';
  public $defaultAction = 'list';
  public $accountTypes = [
    1 => [ // Hajj Members
      'title' => 'Hajj', 'idPrefix' => '1h', 'table' => 'member_transactions',
      'class' => 'MemberTransactions',
      'personTablePath' => ['table' => 'members', 'fk' => 'member_id'],
      'receiptLink' => 'members/printReceipt',
      'receiptNumberMethod' => 'receiptNumber',
      'voucherNumberMethod' => 'refundVoucherNumber',
      'revisionLink' => 'members/reviseTransaction',
      'viewRevisionLink' => 'members/viewTransactionHistory',
    ], 2 => [ // Umra Payees
      'title' => 'Umra', 'idPrefix' => '2u', 'table' => 'umra_transactions',
      'class' => 'UmraTransactions', 'personTablePath' => [
        'table' => 'umra_pilgrims', 'fk' => 'umra_pilgrim_id'
      ], 'receiptLink' => 'umra/umraPaymentReceipt',
      'receiptNumberMethod' => 'umraReceiptNumber',
      'voucherNumberMethod' => 'umraRefundVoucherNumber',
      'revisionLink' => 'umra/reviseUmraTransaction',
      'viewRevisionLink' => 'umra/viewTransactionHistory',
    ], 3 => [ // Non MHCL Hajj Payees
      'title' => 'Non Members', 'idPrefix' => '3n',
      'table' => 'non_mhcl_member_transactions',
      'class' => 'NonMhclMemberTransactions', 'personTablePath' => [
        'table' => 'non_mhcl_members', 'fk' => 'non_mhcl_member_id'
      ], 'receiptLink' => 'nonMembers/printReceipt',
      'receiptNumberMethod' => 'nonMemberReceiptNumber',
      'voucherNumberMethod' => 'nonMemberRefundVoucherNumber',
      'revisionLink' => 'nonMembers/reviseTransaction',
      'viewRevisionLink' => 'nonMembers/viewTransactionHistory',
    ], 4 => [ // Ageega Payments
      'title' => 'Ageega', 'idPrefix' => '4a', 'table' => 'ageega_transactions',
      'class' => 'AgeegaTransactions',
      'personTablePath' => ['table' => 'ageega', 'fk' => 'ageega_id'],
      'receiptLink' => 'ageega/printReceipt',
      'receiptNumberMethod' => 'ageegaReceiptNumber',
      'voucherNumberMethod' => 'ageegaRefundVoucherNumber',
      'revisionLink' => 'ageega/reviseAgeegaTransaction',
      'viewRevisionLink' => 'ageega/viewTransactionHistory',
    ],
  ];

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
      [
        'allow', // allow all users to perform 'index' and 'view' actions
        'actions' => [''], 'users' => ['*'],
      ], [
        'allow',
        // allow authenticated user to perform 'create' and 'update' actions
        'actions' => ['jobApprovals', 'userAgreement'], 'users' => ['@'],
      ], [
        'allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array_merge(Helpers::perms(), ['']), 'users' => ['@'],
      ], [
        'deny', // deny all users
        'users' => ['*'],
      ],
    ];
  }

  public function actionUserAgreement() {
    if (Yii::app()->user->isGuest) {
      $this->redirect(['site/verify']);
    }
    if (!empty($_POST)) {
      $this->person->setAttributes($_POST['Persons'],false);
      $this->person->save();
      if (!$this->person->agreed_to_terms_of_use) {
        Yii::app()->user->logout();
        $this->redirect('http://mhcl.mv');
      }
      $this->redirect(Yii::app()->user->returnUrl);
    }

    $this->layout = "//layouts/column1";
    $this->render('userAgreement');

  }

  public function actionJobAssignments() {

    /** @var Users $curUser */
    $curUser = Users::model()->findByPk(Yii::app()->user->id);
    $userId = $curUser->id;

    if (isset($_POST['rj'])) {
      foreach ($_POST['rj'] as $k => $rj) {
        $requestJob = new UserJobs();
        $requestJob->requested_user_id = Yii::app()->user->id;
        $requestJob->user_id = Yii::app()->user->id;
        $requestJob->job_id = $k;
        $requestJob->requested_datetime = Yii::app()->params['dateTime'];
        $requestJob->save();
      }
    }


    $requestedJobs =
      UserJobs::model()->with('user', 'job')->findAllByAttributes([
          'user_id' => Yii::app()->user->id, 'approved_user_id' => null,
          'cancelled_user_id' => null
        ]);

    $approvedJobs = UserJobs::model()->with('user', 'job')->findAll('user_id = :userId AND approved_user_id IS NOT NULL AND
        cancelled_user_id IS NULL', [':userId' => Yii::app()->user->id]);

    $allUserJobIds = Yii::app()->db->createCommand("
        SELECT job_id from user_jobs
        WHERE user_id = :userId
        AND cancelled_user_id IS NULL
    ")->bindParam(':userId', $userId, PDO::PARAM_INT)->queryColumn();

    $criteria = new CDbCriteria();
    $criteria->addColumnCondition(['organization_id' => $curUser->organization_id]);
    $criteria->addNotInCondition('job_id', $allUserJobIds);

    $availableJobs = OrganizationJobs::model()->findAll($criteria);


    $this->render('jobAssignments', [
      'requestedJobs' => $requestedJobs, 'approvedJobs' => $approvedJobs,
      'availableJobs' => $availableJobs, 'user' => $curUser
    ]);

  }

  public function actionTodaysMemberDocs() {
    $this->checkDocumentsForTodaysCollections((Helpers::hasPerm('allCollections', 'users') ? null : Yii::app()->user->id), false);
  }

  public function actionJobApprovals($id) {


    /** @var Users $curUser */
    $curUser = Users::model()->findByPk(Yii::app()->user->id);
    $curUserId = $curUser->id;

    #region Cannot approve own Job Requests
    if ($curUserId == $id) {
      Yii::app()->user->setFlash('error', 'You cannot approve your own Job
      Requests.');
      $this->redirect(['list']);
    }
    #endregion

    #region Check current user is an admin
    $curUserOrgId = $curUser->organization_id;
    $adminUser = Yii::app()->db->createCommand("
      SELECT id FROM organization_admins
      WHERE user_id = :userId AND organization_id = :orgId AND is_cancelled = 0
    ")->bindParam(':userId', $curUserId, PDO::PARAM_INT)
      ->bindParam(':orgId', $curUserOrgId, PDO::PARAM_INT)->queryColumn();

    //not an admin user
    if (empty($adminUser) && ($curUserId != Helpers::config('devUserId'))) {
      $this->redirect(['list']);
    }
    #endregion

    /** @var Users $targetUser */
    $targetUser = Users::model()->findByPk($id);
    $targetUserId = $targetUser->id;
    $dateTime = Yii::app()->params['dateTime'];
    if (($curUserId <> Helpers::config('devUserId')) &&
      (empty($targetUser) || $targetUser->organization_id <> $curUserOrgId)
    ) {
      $this->redirect(['list']);
    }

    if (isset($_POST['aj'])) {
      foreach ($_POST['aj'] as $k => $aj) {
        Yii::app()->db->createCommand("
          UPDATE user_jobs SET
          approved_user_id = :approvedUserId,
          approved_datetime = :approvedDateTime
          WHERE user_id = :targetUserId
          AND job_id = :jobId
        ")->bindParam(':approvedUserId', $curUserId, PDO::PARAM_INT)
          ->bindParam(':approvedDateTime', $dateTime, PDO::PARAM_STR)
          ->bindParam(':targetUserId', $targetUserId, PDO::PARAM_INT)
          ->bindParam(':jobId', $k, PDO::PARAM_INT)->execute();
      }
    }


    $requestedJobs =
      UserJobs::model()->with('user', 'job')->findAllByAttributes([
          'user_id' => $targetUser->id, 'approved_user_id' => null,
          'cancelled_user_id' => null
        ]);

    $approvedJobs = UserJobs::model()->with('user', 'job')->findAll('user_id = :userId AND approved_user_id IS NOT NULL AND
        cancelled_user_id IS NULL', [':userId' => $targetUser->id]);

    $this->render('jobApprovals', [
      'user' => $targetUser, 'requestedJobs' => $requestedJobs,
      'approvedJobs' => $approvedJobs
    ]);


  }

  private function checkOrgAuthorization($targetUserId) {
    if (!Helpers::hasPerm('manageAllOrgUsers')) {
      /** @var Users $curUser */
      /** @var Users $targetUser */
      $curUser = Users::model()->findByPk(Yii::app()->user->id);
      $targetUser = Users::model()->findByPk((int)$targetUserId);
      if ($curUser->organization_id <> $targetUser->organization_id ||
        ($curUser->branch_id <> null &&
          $curUser->branch_id <> $targetUser->branch_id)
      ) {
        Yii::app()->user->setFlash('error', 'Access denied.');
        $this->redirect(['users/list']);
      }
    }

  }

  public function actionPermissions($id) {

    $this->checkOrgAuthorization($id);

    if ($id == Yii::app()->user->id &&
      Yii::app()->user->id != Helpers::config('devUserId')
    ) {
      Yii::app()->user->setFlash('error', 'You cannot edit your own permissions!');
      $this->redirect(['users/list']);
    }

    if ($_POST) {
      UserPermissions::model()
        ->deleteAllByAttributes(['user_id' => (int)$_POST['id']]);
      if (!empty($_POST['p'])) {
        foreach ($_POST['p'] as $p => $v) {
          $up = new UserPermissions();
          $up->user_id = (int)$_POST['id'];
          $up->app_function_id = $p;
          $dbTransaction = Yii::app()->db->beginTransaction();
          $up->save();
          $dbTransaction->commit();
        }
      }
      Yii::app()->user->setFlash('success', 'Permissions updated successfully!');
    }

    $permissionGroups =
      PermissionGroups::model()->with('permissionGroupAppFunctions')
        ->findAll(['order' => 'permission_order asc']);
    $userPermissions =
      Yii::app()->db->createCommand('SELECT app_function_id from user_permissions WHERE user_id = ' .
        (int)$id)->queryColumn();
    $userModel = Users::model()->findByPk($id);

    $this->render('permissions', [
      'permissionGroups' => $permissionGroups,
      'userPermissions' => $userPermissions, 'userModel' => $userModel
    ]);
  }

  public function actionJobs() {
    $jobs = Jobs::model()->with('jobAppFunctions')->findAll();
    $this->render('jobs', ['jobs' => $jobs]);
  }

  public function actionEditJob($id) {

    /** @var Jobs $job */
    $job = Jobs::model()->findByPk($id);
    if ($_POST) {
      JobAppFunctions::model()
        ->deleteAllByAttributes(['job_id' => (int)$_POST['id']]);
      foreach ($_POST['p'] as $p => $v) {
        $up = new JobAppFunctions();
        $up->job_id = (int)$_POST['id'];
        $up->app_function_id = $p;
        $dbTransaction = Yii::app()->db->beginTransaction();
        $up->save();
        $dbTransaction->commit();
      }
      Yii::app()->user->setFlash('success', $job->name_english .
        '\'s job permissions updated successfully!');
      $this->redirect(['jobs']);
    }

    $permissionGroups =
      PermissionGroups::model()->with('permissionGroupAppFunctions')->findAll();
    $jobPermissions =
      Yii::app()->db->createCommand("SELECT app_function_id from job_app_functions where job_id = :jobId")
        ->bindParam(':jobId', $id, PDO::PARAM_INT)->queryColumn();

    $this->render('editJob', [
      'permissionGroups' => $permissionGroups,
      'jobPermissions' => $jobPermissions, 'job' => $job
    ]);


  }

  public function actionOrganizations() {
    $organizations = Organizations::model()->with('organizationJobs')
      ->findAll(['order' => 't.name_english asc']);
    $this->render('organizations', ['organizations' => $organizations]);
  }

  public function actionEditOrg($id) {

    /** @var Organizations $org */
    $org = Organizations::model()->findByPk($id);

    $allJobs = Jobs::model()->findAll();

    $users = $org->users;

    if ($_POST) {
      $dbTransaction = Yii::app()->db->beginTransaction();

      // new organization_jobs (delete current jobs first)
      OrganizationJobs::model()->deleteAllByAttributes([
        'organization_id' => (int)$_POST['id']
      ]);
      if (!empty($_POST['j'])) {
        foreach ($_POST['j'] as $j => $v) {
          $oj = new OrganizationJobs();
          $oj->organization_id = (int)$_POST['id'];
          $oj->job_id = $j;
          $oj->save();
        }
      }

      // new organization admins (delete current admins first)
      OrganizationAdmins::model()->deleteAllByAttributes([
        'organization_id' => (int)$_POST['id']
      ]);


      $orgUsers =
        Yii::app()->db->createCommand("select id from users where organization_id = :orgId")
          ->bindParam(':orgId', $id, PDO::PARAM_INT)->queryColumn();

      $manageOrgUsersFunctionId = Constants::MANAGE_ORG_USERS_FUNCTION_ID;
      Yii::app()->db->createCommand('
        delete from user_permissions
        where user_id in (' . implode(',', $orgUsers) . ')
         and app_function_id = :appFunctionId')
        ->bindParam(':appFunctionId', $manageOrgUsersFunctionId, PDO::PARAM_INT)
        ->execute();

      if (!empty($_POST['a'])) {
        foreach ($_POST['a'] as $a => $v) {
          $oa = new OrganizationAdmins();
          $oa->organization_id = (int)$_POST['id'];
          $oa->user_id = $a;
          $oa->save();

          $up = new UserPermissions();
          $up->user_id = $oa->user_id;
          $up->app_function_id = Constants::MANAGE_ORG_USERS_FUNCTION_ID;
          $up->save();
        }
      }


      $dbTransaction->commit();

      Yii::app()->user->setFlash('success', $org->name_english . ' jobs &
      Admins updated successfully!');
      $this->redirect(['organizations']);


    }

    $orgJobIds = Yii::app()->db->createCommand("
      SELECT job_id from organization_jobs WHERE organization_id = :orgId
    ")->bindParam(':orgId', $id, PDO::PARAM_INT)->queryColumn();

    $orgAdmins = Yii::app()->db->createCommand("
      SELECT user_id from organization_admins WHERE organization_id = :orgId
    ")->bindParam(':orgId', $id, PDO::PARAM_INT)->queryColumn();

    $this->render('editOrg', [
      'orgJobIds' => $orgJobIds, 'orgAdmins' => $orgAdmins,
      'allJobs' => $allJobs, 'users' => $users, 'org' => $org
    ]);

  }

  public function actionAllCollections() {

    $today = (new DateTime('today'))->format('d M Y H:i');
    $tomo = (new DateTime('tomorrow'))->format('d M Y H:i');
    $collectionFilter = empty($_GET["CollectionFilter"]) ? [
      'fromTime' => $today, 'tillTime' => $tomo, 'userId' => null,
      'export' => null, 'amountType' => null, 'accountType' => null,
      'descriptionText' => null, 'orgId' => null
    ] : $_GET["CollectionFilter"];

    $this->collections($collectionFilter);
  }

  public function actionCollections() {
    $today = (new DateTime('today'))->format('d M Y H:i');
    $tomo = (new DateTime('tomorrow'))->format('d M Y H:i');
    $collectionFilter = [
      'fromTime' => $today, 'tillTime' => $tomo,
      'userId' => Yii::app()->user->id, 'export' => null, 'amountType' => null,
      'accountType' => null, 'descriptionText' => null, 'orgId' => null
    ];

    $this->collections($collectionFilter);

  }

  private function checkDocumentsForTodaysCollections($userId = null,
    $onlyMissing = true) {

    $userIdCriteria = "";
    if (empty($userId) && !Helpers::hasPerm("allOrgCollections")) {
      /** @var Users $curUser */
      $curUser = Users::model()->findByPk(Yii::app()->user->id);
      $orgBranchCriteria =
        empty($curUser->branch_id) ? (" AND organization_id = " .
          (int)$curUser->organization_id) : (" AND branch_id = " .
          (int)$curUser->branch_id);
      $userIds = Yii::app()->db->createCommand("
        SELECT id FROM users
        WHERE 1 $orgBranchCriteria
      ")->queryColumn();
      $userIdCriteria = " AND user_id in (" . implode(",", $userIds) . ")";
    } else {
      $userIdCriteria = " AND user_id = :userId";
    }

    $memberIdsToday = Yii::app()->db->createCommand("
      SELECT DISTINCT member_id
      FROM member_transactions
      WHERE is_cancelled = 0 $userIdCriteria AND
        DATE_FORMAT(transaction_time, '%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')")
      ->bindParam(':userId', $userId, PDO::PARAM_INT)->queryColumn();


    $dpCriteria = new CDbCriteria();
    $dpCriteria->with = ['applicationForm', 'person'];
    $dpCriteria->addInCondition('t.id', $memberIdsToday);
    $dpCriteria->addColumnCondition(['membership_date' => (new DateTime())->format('y-m-d')]);
    $memberModels = new CActiveDataProvider('Members', [
      'criteria' => $dpCriteria,
      'pagination' => ['pageSize' => Helpers::config('pageSize')], 'sort' => [
        'defaultOrder' => 't.id asc',
        'attributes' => ['t.id', 'person.id_no', 'person.full_name_english']
      ]
    ]);
    $checkMissingCriteria = clone $dpCriteria;
    $checkMissingCriteria->addCondition('applicationForm.application_form IS NULL or
    applicationForm.id_copy IS NULL');
    if (empty(Members::model()->findAll($checkMissingCriteria)) &&
      $onlyMissing
    ) {
      return;
    }
    $this->render('missingDocs', ['memberModels' => $memberModels]);
    Yii::app()->end();
  }

  private function collections($collectionFilter) {

    /** @var Users $curUser */
    $curUser = Users::model()->findByPk(Yii::app()->user->id);

    $fromTime =
      (new DateTime($collectionFilter['fromTime']))->format(Constants::DATETIME_SAVE_FORMAT);
    $tillTime =
      (new DateTime($collectionFilter['tillTime']))->format(Constants::DATETIME_SAVE_FORMAT);
    $userId = $collectionFilter['userId'];
    $orgId =
      Helpers::hasPerm('allOrgCollections') ? $collectionFilter['orgId'] : $curUser->organization_id;
    $export = $collectionFilter['export'];
    $amountType = $collectionFilter['amountType'];
    $accountType = $collectionFilter['accountType'];
    $descriptionText = trim($collectionFilter['descriptionText']);

    $userIdCriteria = "";
    $orgCriteria = "";
    $amountTypeCriteria = "";
    $cancellationCriteria = "";
    $descriptionCriteria = "";

    if (empty($fromTime)) {
      $fromTime =
        (new DateTime('today'))->format(Constants::DATETIME_SAVE_FORMAT);
    }
    if (empty($tillTime)) {
      $tillTime =
        (new DateTIme('tomorrow'))->format(Constants::DATETIME_SAVE_FORMAT);
    }

    $params = [':fromTime' => $fromTime, ':tillTime' => $tillTime];


    if (!empty($orgId)) {
      $orgCriteria = " AND (u.organization_id = :orgId)";
      $params[':orgId'] = $orgId;
    }

    if (!empty($userId)) {
      $userIdCriteria = " AND (user_id = :userId)";
      $params[':userId'] = $userId;
    }

    if (!empty($amountType)) {
      $amountTypeCriteria =
        $amountType == 1 ? " AND amount < 0" : " AND amount > 0";
    }

    if (!empty($cancellation)) {
      $cancellationCriteria =
        $cancellation == 1 ? " AND cancelled = 1" : " AND cancelled = 0";
    }

    if (!empty($descriptionText)) {
      $descriptionCriteria = " AND description_english LIKE
      CONCAT('%', :descriptionText, '%')";
      $params[':descriptionText'] = $descriptionText;
    }

    $totalsSqlTables = "";
    $dataSqlTables = "";
    foreach ($this->accountTypes as $key => $acctType) {
      if (empty($accountType) || $accountType == $key) {
        $totalsSqlTables .= (empty($totalsSqlTables) ? " " : " UNION");
        $totalsSqlTables .= "
          SELECT transaction_id, transaction_medium_id, amount
          FROM {$acctType["table"]}
          JOIN users u ON {$acctType["table"]}.user_id = u.id
          WHERE (transaction_time BETWEEN :fromTime AND :tillTime)
            AND is_cancelled = 0 $userIdCriteria $amountTypeCriteria
            $descriptionCriteria $orgCriteria
          ";

        $dataSqlTables .= (empty($dataSqlTables) ? " " : " UNION");
        $dataSqlTables .= "
          SELECT '{$acctType["idPrefix"]}' as t_type, transaction_id,
            CONCAT('{$acctType["idPrefix"]}',transaction_id) as t_id,
            p.id_no as id_no, p.full_name_english, description_english,
            transaction_time, transaction_medium_id, amount, balance, user_id,
            user_name, is_cancelled, o.membership_prefix,
            '{$acctType["receiptLink"]}' as rLink
          FROM {$acctType["table"]} trans
          JOIN {$acctType["personTablePath"]["table"]} pt
            ON pt.id = trans.{$acctType["personTablePath"]["fk"]}
          JOIN persons p on pt.person_id = p.id
          JOIN users u on trans.user_id = u.id
          JOIN organizations o on u.organization_id = o.id
          WHERE (transaction_time BETWEEN :fromTime AND :tillTime)
            $cancellationCriteria $userIdCriteria $orgCriteria
            $amountTypeCriteria $descriptionCriteria
        ";
      }
    }

    $totalsSql = "
      SELECT tm.name_english medium, SUM(t.amount) sum_amount
      FROM ($totalsSqlTables) t
      JOIN z_transaction_mediums tm ON tm.id = t.transaction_medium_id
      GROUP BY t.transaction_medium_id
    ";

    $dataSql = "
      SELECT t.*, tm.name_english
      FROM ($dataSqlTables) t
      JOIN z_transaction_mediums tm ON tm.id = t.transaction_medium_id

    ";

    $count =
      Yii::app()->db->createCommand('SELECT COUNT(id_no) FROM (' . $dataSql .
        ') t')->queryScalar($params);


    $data = Yii::app()->db->createCommand($dataSql)->queryAll(true, $params);

    if (!empty($export) && $export == 1) {

      // sort data if user has sorted the grid (CGridView adds 'sort' to url
      // in the format sort_key.direction)
      $sort =
        explode(" ", str_replace(".", " ", (!empty($_GET['sort']) ? $_GET['sort'] : 'transaction_time asc')));
      $sortKey = $sort[0];
      $dir = isset($sort[1]) ? $sort[1] : 'asc';
      $dir = ($dir == 'asc') ? 1 : -1;
      usort($data, function ($a, $b) use ($dir, $sortKey) {
        return $dir * (($a[$sortKey] < $b[$sortKey]) ? -1 : 1);
      });

      $xlData = [];
      $i = 0;
      foreach ($data as $dataItem) {

        $atKey = (int)substr($dataItem["t_type"], 0, 1);
        $modelClass = $this->accountTypes[$atKey]['class'];
        $transaction =
          $modelClass::model()->findByPk($dataItem["transaction_id"]);
        $receiptMethod = $this->accountTypes[$atKey][$dataItem["amount"] <
        0 ? 'voucherNumberMethod' : 'receiptNumberMethod'];

        $xlData[] = [
          '#' => ++$i, 'receipt' => Helpers::$receiptMethod($transaction),
          'time' => $dataItem["transaction_time"],
          'member' => $dataItem["id_no"] . ', ' .
            $dataItem["full_name_english"],
          'transaction_details' => $dataItem["description_english"],
          'medium' => $dataItem["name_english"],
          'amount' => $dataItem["amount"],
          'cancelled' => $dataItem["is_cancelled"] ? 'Cancelled' : '',
          'user_id' => $dataItem["user_id"], 'user' => $dataItem["user_name"],
          'org' => $dataItem["membership_prefix"],
        ];
      }
      /** @var Users $user */
      $user = Users::model()->findByPk(Yii::app()->user->id);
      array_unshift($xlData, ['Collections Filter Results'], [''], [
          'From: ' . (new DateTime($fromTime))->format('d M Y H:i')
        ], [
          'Till: ' . (new DateTime($tillTime))->format('d M Y H:i')
        ], ['user_id: ' . ((empty($userId)) ? 'All' : $userId)], [
          'org: ' . ((empty($orgId)) ? 'All' : $orgId)
        ], [''], [
          'Generated By: ' . $user->person->full_name_english . ', on: ' .
          date('d F Y H:i:s')
        ], [''], [
          '#', 'Receipt/Refund Voucher Number', 'Time', 'Member',
          'Transaction Details', 'Medium', 'Amount', 'Cancelled?', 'User ID',
          'User', 'Organization'
        ]);

      $title = "Collections_filter_list_" . date('Ymd-His');

      Yii::import('ext.csv.CSVFileDownload');
      $csvFile = new CSVFileDownload;
      $csvFile->generateCSV($title, $xlData);
      Yii::app()->end();
    }


    foreach ($data as $key => $dataItem) {
      $atKey = (int)substr($dataItem["t_type"], 0, 1);
      $data[$key]['is_cancelled'] =
        (int)($dataItem['is_cancelled'] && $dataItem['is_cancelled'] != "\0");
      $data[$key]['revisionLink'] = $this->accountTypes[$atKey]['revisionLink'];
      $data[$key]['viewRevisionLink'] =
        $this->accountTypes[$atKey]['viewRevisionLink'];
    }


    $dp = new CArrayDataProvider($data, [
      'totalItemCount' => $count,
      'keyField' => 't_id', 'sort' => [
        'defaultOrder' => 'transaction_time asc', 'attributes' => [
          'id_no', 'transaction_time', 'name_english', 'description_english',
          'amount', 'user_name', 'membership_prefix'
        ],
      ], 'pagination' => ['pageSize' => Helpers::config('pageSize')]
    ]);

    $totals =
      Yii::app()->db->createCommand($totalsSql)->queryAll(true, $params);

    $this->render('collections', [
      'dp' => $dp, 'totals' => $totals, 'curUser' => $curUser,
      'filter' => $collectionFilter
    ]);
  }

  public function actionApproveRevision() {
    header('HTTP/1.1 200 OK');
    header('Content-type: text/html');

    if (!empty($_POST)) {
      /** @var RevisionRequests $revision */
      $revision = RevisionRequests::model()->findByPk($_POST['revision_id']);
      if (!empty($revision)) {
        if ($revision->transaction->is_cancelled) {
          echo CJSON::encode(['status' => 'failed']);
          Yii::app()->end();
        }
        try {
          if ($_POST['decision']) {
            $revision->approve();
          } else {
            $revision->disapprove();
          }
          echo CJSON::encode(['status' => 'success']);
          Yii::app()->end();

        } catch (CException $ex) {
          ErrorLog::exceptionLog($ex);
          echo CJSON::encode(['status' => 'failed']);
        }

      }
    }
    echo CJSON::encode(['status' => 'failed']);
    Yii::app()->end();

  }

  public function actionRevisions() {
//    $this->layout= '//layouts/column1';
    $revisionsDp = new CActiveDataProvider('RevisionRequests', [
      'criteria' => ['with' => ['requestedBy', 'confirmedBy']],
      'sort' => ['defaultOrder'=>'t.id desc',
                 'attributes'=> ['requestedBy.user_name']],
      'pagination' => false
    ]);
    $this->render('revisionRequests', compact('revisionsDp'));
  }

  public function actionRevisionRequest($toCancel = false) {
    header('HTTP/1.1 200 OK');
    header('Content-type: text/html');

    if (empty($_POST)) {
      echo CJSON::encode(['status' => 'failed']);
    } else {
      $revisionRequest = new RevisionRequests();
      $revisionRequest->setAttributes($_POST);
      $revisionRequest->requested_by = Yii::app()->user->id;
      $revisionRequest->requested_datetime = Yii::app()->params['dateTime'];
      $revisionRequest->to_be_cancelled = (int) $toCancel;
      if ($revisionRequest->validate()) {
        try {
          $revisionRequest->save();
          echo CJSON::encode(['status' => 'success']);
        } catch (CException $ex) {
          echo CJSON::encode(['status' => 'failed']);
          ErrorLog::exceptionLog($ex);
        }
      } else {
        echo CJSON::encode(['status' => 'failed']);
      }
    }
    Yii::app()->end();
  }

  public function actionMyRevisionRequests() {
    $revisionsDp = new CActiveDataProvider('RevisionRequests', [
      'criteria' =>[
        'condition' => 'requested_by = :userId',
         'params' => [':userId' => Yii::app()->user->id]
      ],
      'sort' => ['defaultOrder' => 't.id desc',
                 'attributes' => ['requestedBy.user_name']
      ],
      'pagination' => false
    ]);
    $this->render('revisionRequests', compact('revisionsDp'));

  }

  public static function getReceiptNo($dataItem) {

    switch ($dataItem["t_type"]) {
      case "1h":
        $tr =
          MemberTransactions::model()->findByPk($dataItem["transaction_id"]);

        return $dataItem["amount"] >
        0 ? Helpers::receiptNumber($tr) : Helpers::refundVoucherNumber($tr);
        break;
      case "2u":
        $tr = UmraTransactions::model()->findByPk($dataItem["transaction_id"]);

        return $dataItem["amount"] >
        0 ? Helpers::umraReceiptNumber($tr) : Helpers::umraRefundVoucherNumber($tr);
        break;
      case "3n":
        $tr = NonMhclMemberTransactions::model()
          ->findByPk($dataItem["transaction_id"]);

        return $dataItem["amount"] >
        0 ? Helpers::nonMemberReceiptNumber($tr) : Helpers::nonMemberRefundVoucherNumber($tr);
        break;
      case "4a":
        $tr =
          AgeegaTransactions::model()->findByPk($dataItem["transaction_id"]);

        return Helpers::ageegaReceiptNumber($tr);
        break;
    }
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view'
   * page.
   */
  public function actionCreate() {
    $userModel = new Users;
    $personModel = new Persons;
    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['Users']) || isset($_POST['Persons'])) {

      $userModel->setAttributes($_POST['Users']);
      $validated = $userModel->validate();
      // check if a person was selected
      if (empty($_POST['Users']['person_id'])) {
        $personModel->setAttributes($_POST['Persons']);
        $validated = $personModel->validate() && $validated;
      }


      if ($validated) {
        // initialize user
        $userModel->user_secret = uniqid();
        $userModel->user_state_id = Constants::USER_CREATED;
        $userModel->login_code = 'xx'; //Helpers::generateLoginCode($userModel);

        $dbTransaction = Yii::app()->db->beginTransaction();

        try {
          if (empty($_POST['Users']['person_id'])) {
            $personModel->save(false);
            $userModel->person_id = $personModel->id;
          }
          $userModel->save(false);
          $selfUpdateUP = new UserPermissions();
          $selfUpdateUP->user_id = $userModel->id;
          $selfUpdateUP->app_function_id =
            Constants::UPDATE_OWN_INFO_FUNCTION_ID;
          $selfUpdateUP->save(); // no need to check if duplicate as this is a new user
          $dbTransaction->commit();

          if (!Helpers::generateLoginCode($userModel, $userModel->user_secret)) {
            Yii::app()->user->setFlash('error', 'User "' .
              $userModel->user_name .
              '" has been created but there was an error sending Activation Code');
          } else {
            Yii::app()->user->setFlash('success', 'User "' .
              $userModel->user_name . '" has been created');
            //            Yii::app()->user->setFlash('success', 'User "' . $userModel->user_name . '" has been created. An activation code has been sent to '
            //              . 'the user\'s mobile' . (Helpers::config('sendUserCodeToEmail') ? ' and email address' : '.'));
          }
          $this->redirect('list');
        } catch (Exception $ex) {
          $dbTransaction->rollback();
          Yii::app()->user->setFlash('error', 'The user was not created due to an error. Please try again!');
        }
      }
    }

    $this->render('create', [
      'userModel' => $userModel, 'personModel' => $personModel,
    ]);
  }

  public function actionSelfUpdate() {
    $this->updateUser(Yii::app()->user->id);
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id) {
    $this->checkOrgAuthorization($id);
    $this->updateUser($id);
  }

  private function updateUser($id) {

    $this->checkOrgAuthorization($id);

    $userModel = new UserUpdate;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);
    $user = $this->loadModel($id);
    $userModel->id = $user->id;
    $userModel->user_name = $user->user_name;
    $userModel->mobile_number = $user->mobile_number;
    $userModel->email = $user->email;

    if (isset($_POST['UserUpdate'])) {

      $userModel->setAttributes($_POST['UserUpdate'], false);
      if ($userModel->validate()) {
        $user->user_name = $userModel->user_name;
        $passwordNotUpdatedMessage = '';
        if (!empty($userModel->password)) {
          $user->user_secret = md5($userModel->password);
        } else {
          $passwordNotUpdatedMessage = ' Password was not updated!';
        }
        $user->mobile_number = $userModel->mobile_number;
        $user->email = $userModel->email;
        $dbTransaction = Yii::app()->db->beginTransaction();
        if ($user->save()) {
          $dbTransaction->commit();
          Yii::app()->user->setFlash('success', 'User information for ' .
            $user->person->full_name_english . ' has been updated!' .
            $passwordNotUpdatedMessage);
          $this->redirect(['list']);
        } elseif ($user->hasErrors()) {
          foreach ($user->errors as $attribute => $errors) {
            foreach ($errors as $error) {
              $userModel->addError($attribute, $error);
            }
          }
        }
      }
    }

    $this->render('update', [
      'userModel' => $userModel, 'user' => $user
    ]);
  }

  /**
   * Manages all models.
   */
  public function actionList($orgId = null) {
    $criteria = new CDbCriteria();
    $criteria->condition = 'id <> :devId';
    $criteria->params[':devId'] = (int)Helpers::config('devUserId');
    if (Helpers::hasPerm('manageAllOrgUsers')) {
      if (!empty($orgId)) {
        $criteria->addColumnCondition(['organization_id' => $orgId]);
      }
    } else {
      /** @var Users $curUser */
      $curUser = Users::model()->findByPk(Yii::app()->user->id);
      $criteria->addColumnCondition([
        'organization_id' => $curUser->organization_id
      ]);
      if (!empty($curUser->branch_id)) {
        $criteria->addColumnCondition(['branch_id' => $curUser->branch_id]);
      }
    }
    $dataProvider = new CActiveDataProvider('Users', [
      'criteria' => $criteria, 'pagination' => false,
    ]);

    $userCount['locked'] = Yii::app()->db->createCommand("
        SELECT count(id) FROM users
        WHERE user_state_id = :locked
          AND id <> :devUserId")->queryScalar([
      ':locked' => Constants::USER_LOCKED,
      ':devUserId' => (int)Helpers::config('devUserId')
    ]);
    $userCount['unlocked'] = Yii::app()->db->createCommand("
        SELECT count(id) FROM users
        WHERE user_state_id <> :locked
          AND id <> :devUserId")->queryScalar([
      ':locked' => Constants::USER_LOCKED,
      ':devUserId' => (int)Helpers::config('devUserId')
    ]);


    $this->render('list', [
      'dataProvider' => $dataProvider, 'userCount' => $userCount
    ]);

  }


  public function actionLockUser($id) {
    $this->checkOrgAuthorization($id);
    $model = $this->loadModel($id);
    $model->user_state_id = Constants::USER_LOCKED;
    $dbTransaction = Yii::app()->db->beginTransaction();
    $model->save();
    $dbTransaction->commit();
    $this->redirect(['users/list']);
  }

  public function actionUnlockUser($id) {
    $this->checkOrgAuthorization($id);
    $model = $this->loadModel($id);
    $model->user_state_id = Constants::USER_ACTIVE;
    $dbTransaction = Yii::app()->db->beginTransaction();
    $model->save();
    $dbTransaction->commit();
    $this->redirect(['users/list']);
  }

  public function actionSendNewCode($id) {
    $this->checkOrgAuthorization($id);
    $model = $this->loadModel($id);
    if (Helpers::generateLoginCode($model)) {
      Yii::app()->user->setFlash('success', 'A new code has been sent to ' .
        $model->person->full_name_english);
    } else {
      Yii::app()->user->setFlash('error', 'There was an error sending the code. Please try again!');
    }
    $this->redirect(['users/list']);
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   *
   * @param integer $id the ID of the model to be loaded
   *
   * @return Users the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Users::model()->findByPk($id);
    if ($model === null or ($model->id == Helpers::config('devUserId') &&
        Yii::app()->user->id <> Helpers::config('devUserId'))
    ) {
      throw new CHttpException(404, 'The requested page does not exist.');
    }

    return $model;
  }

  /**
   * Performs the AJAX validation.
   *
   * @param Users $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'users-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
