<?php

class AgeegaController extends Controller {

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
        'actions' => ['list', 'register', 'ageegaPayment'],
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

  public function actionManageRates() {
    $ageegaRatesDp = new CActiveDataProvider('AgeegaRates',
      ['criteria' => ['order' => 'id desc']]);
    $this->render('manageRates', ['ageegaRatesDp' => $ageegaRatesDp]);
  }

  public function actionList() {
    if (empty($this->person->ageegas))
      $this->redirect(['ageega/register']);

    $ageegaDp = new CActiveDataProvider('Ageega', [
      'criteria' => ['condition' => 'person_id = ' . $this->person->id],
      'pagination' => false
    ]);
    $this->render('list', compact('ageegaDp'));


  }

  public function actionCreateAgeegaRate() {
    $ageegaRate = new AgeegaRates();

    if (!empty($_POST['AgeegaRates'])) {
      $ageegaRate->setAttributes($_POST['AgeegaRates']);
      if ($ageegaRate->save())
        $this->redirect(['manageRates']);
    }


    $this->render('ageegaRateForm', ['ageegaRate' => $ageegaRate]);
  }

  public function actionEditAgeegaRate($id) {
    $ageegaRate = AgeegaRates::model()->findByPk($id);
    if (empty($ageegaRate))
      $this->redirect('manageRates');
    if (!empty($_POST['AgeegaRates'])) {
      $ageegaRate->setAttributes($_POST['AgeegaRates']);
      if ($ageegaRate->save())
        $this->redirect(['manageRates']);
    }
    $this->render('ageegaRateForm', ['ageegaRate' => $ageegaRate]);
  }

  public function actionRegister() {
    if (!$this->processPayments)
    {
      $this->render('startsFebruary');
      Yii::app()->end();
    }
    $ageega = new Ageega();
    $ageegaReasons = CHtml::listData(ZAgeegaReasons::model()->findAll(),'id',
      H::tf('name_dhivehi'));
    $children = [];
    $sheepQty = null;
    $docsArray = ['ageega_form'];
    if (Yii::app()->session->contains('ageegaForm')) {
      $ageegaForm = Yii::app()->session->get('ageegaForm');
      $ageega = $ageegaForm['ageega'];
      $children = $ageegaForm['children'];
      $sheepQty = $ageegaForm['sheepQty'];
      Yii::app()->session->remove('ageegaForm');
    }

    if (!empty($_POST['Ageega'])) {
      $valid = true;
      //region Ageega record
      $ageega->setAttributes($_POST['Ageega']);
      $ageega->person_id = $this->person->id;
      $ageega->phone_number = $this->person->phone;

      //endregion

      //region Ageega children records
      // ageega requires child records
      // if reason is not for children naming this is not a childrenForm
      $childrenForm =
        $ageega->ageega_reason_id == Constants::AGEEGA_REASON_CHILDREN_NAMING;

//      if ($childrenForm && empty(sizeof($_POST['full_name_english'])))
//        $ageega->addError('main', 'ކުދިންގެ ތަފްޞީލް ފުރުއްވާ');

      if ($childrenForm) {
        $validChild = true;
        foreach ($_POST['full_name_english'] as $key => $child_name) {
          if (trim($_POST['full_name_english'][$key]
              . $_POST['full_name_dhivehi'][$key]
              . $_POST['birth_certificate_no'][$key] . $_POST['sheep_qty'][$key]
              . $_POST['gender_id'][$key]) == "1"
          )
            continue;
          $ageegaChild = [
            'full_name_english' => $_POST['full_name_english'][$key],
            'full_name_dhivehi' => $_POST['full_name_dhivehi'][$key],
            'gender_id' => $_POST['gender_id'][$key],
            'birth_certificate_no' => $_POST['birth_certificate_no'][$key],
            'sheep_qty' => $_POST['sheep_qty'][$key]
          ];
          $child = new AgeegaChildren();
          $child->setAttributes($ageegaChild);
          /** @var ageegaChildren[] $children */
          $validChild = $child->validate() && $validChild;
          $valid = $valid && $validChild;
          $children[] = $child;
        }
        if (empty($children)) {
          $ageega->addError('main', H::t('ageega','fillChildrenDetails'));
          $valid = false;
        }

        if (!$validChild) {
          $ageega->addError('main', H::t('ageega', 'correctChildren'));
        }
      }

      //endregion

      if (!$childrenForm) {
        $sheepQty = $_POST['sheepQty'];
        $sheepQtyError = empty($sheepQty)?'error':'';
        $valid = $valid && !empty($sheepQty);
        if (empty($sheepQty))
          $ageega->addError('main', H::t('ageega','enterSheepQty'));

      }

      //region Audit and save records
        try {
          //region save uploaded file
          $modelClass = get_class($ageega);
          foreach ($_FILES[$modelClass]['name'] as $fileAttribute => $fileName) {
            $error = $_FILES[$modelClass]['error'][$fileAttribute];

            // if file was not collected by the system
            if ($error > 0) {

              // if file was not provided by user
              if (empty($fileName)) {

                // add error if this is a required file
                $ageega->validate([$fileAttribute], false);
              } else { // if file was uploaded but rejected
                $ageega->addError($fileAttribute, $ageega->getAttributeLabel($fileAttribute) . ' (' . $_FILES[$modelClass]['name'][$fileAttribute] . ') was not accepted!'
                  . ' File may be larger than server limit.');
              }
            } else { // file was collected by system
              // unique file name
              $fileName = uniqid() . '_' . $this->person->id_no . '_' .
                $fileName;

              $valid = $valid && true; // by default
              // check if a file already exists, and if so get path & delete it;
              // do no accept file only if current file could be deleted!
              if (!empty($ageega[$fileAttribute]) && !Helpers::deleteUploadedFile($ageega[$fileAttribute])) {
                $valid = false; // Do not accept file
                Yii::app()->user->setFlash('error', 'There has been an internal error! Could not replace existing file!');
              }

              if ($valid) {
                $ageega[$fileAttribute] = $fileName;
                move_uploaded_file($_FILES[$modelClass]['tmp_name'][$fileAttribute], Yii::app()->params['uploadPath'] . $fileName);
              }
            }
          }

          //endregion
          if ($valid && empty($ageega->errors)) {
            //region Save form details to session
            Yii::app()->session->add('ageegaForm', [
                'ageega' => $ageega,
                'children' => $children,
                'sheepQty' => $sheepQty
              ]);
            //endregion

            $this->redirect(['ageegaPayment']);
          }

        } catch (CException $ex) {
          if (!empty($dbTransaction)) $dbTransaction->rollback();
          ErrorLog::exceptionLog($ex);
          Yii::app()->user->setFlash('error', 'There has been an error. Please try again later!');
        }
      //endregion
    }
    //region Display Ageega Payment Form
    $this->render('ageegaPaymentDetails', [
      'ageega' => $ageega,
      'ageegaReasons' => $ageegaReasons,
      'children' => !empty($children) ? $children : [new AgeegaChildren()],
      'docsArray' => $docsArray,
      'sheepQty' => empty($sheepQty)?0:$sheepQty,
      'sheepQtyError' => empty($sheepQtyError)?'':$sheepQtyError
    ]);
    //endregion
  }

  public function actionAgeegaPayment() {
    if (Yii::app()->session->contains('ageegaForm')) {
      $ageegaForm = Yii::app()->session->get('ageegaForm');
      /** @var Ageega $ageega */
      $ageega = $ageegaForm['ageega'];
      /** @var AgeegaChildren[] $children */
      $children = $ageegaForm['children'];
      $sheepQty = $ageegaForm['sheepQty'];
      $this->render('ageegaPayment', [
        'ageega' => $ageega,
        'children' => !empty($children) ? $children : [new AgeegaChildren()],
        'sheepQty' => !empty($sheepQty) ? $sheepQty: 0,
      ]);
    } else
      $this->redirect(['register']);
  }



}