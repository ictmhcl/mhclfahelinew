<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base
 * class.
 */
class Controller extends CController {
  /**
   * @var string the default layout for the controller view. Defaults to
   *      '//layouts/column1', meaning using a single column layout. See
   *      'protected/views/layouts/column1.php'.
   */
  public $layout = '//layouts/column1';
  /**
   * @var array context menu items. This property will be assigned to {@link
   *      CMenu::items}.
   */
  public $menu = [];
  /**
   * @var array the breadcrumbs of the current page. The value of this property
   *      will be assigned to {@link CBreadcrumbs::links}. Please refer to
   *      {@link CBreadcrumbs::links} for more details on how to specify this
   *      property.
   */
  public $breadcrumbs = [];

  public $requestMethod = null;
  /**
   * @var Persons|null $person
   */
  public $person = null;
  public $processPayments = null;

  public function init() {
//    Yii::app()->session['lang'] = 'en';
    if (!isset(Yii::app()->session['lang']))
      Yii::app()->session['lang'] = Yii::app()->language;
    else
      Yii::app()->language = Yii::app()->session['lang'];

      Yii::app()->params['dateTime'] =
        (new DateTime())->format(Constants::DATETIME_SAVE_FORMAT);
      Yii::app()->params['date'] =
        (new DateTime(Yii::app()->params['dateTime']))->format(Constants::DATE_SAVE_FORMAT);
      /** @var Users $user */
    if (!Yii::app()->user->isGuest) {
      $serverPort = $_SERVER['SERVER_PORT'];
      if (443 != $serverPort) {
        Yii::app()->user->logout();
        $this->refresh();
      }
      $this->person = Persons::model()->findByPk(Yii::app()->user->id);
		
      $this->processPayments = $GLOBALS['cfg']['processPayments'];
      Yii::app()->params['person'] = $this->person;
      if (!$this->person->agreed_to_terms_of_use
          && !in_array(Yii::app()->request->pathInfo,['users/userAgreement',
                                                      'site/logout',
                                                      'site/lang'])
        ) {
        Yii::app()->user->returnUrl = Yii::app()->request->url;
        Yii::app()->controller->redirect(['users/userAgreement']);
      }

    }



    // set filepath params for application
    $fileUploadFolder = $GLOBALS['cfg']['fileUploadParentFolder'];
    $slash = (substr(PHP_OS, 0, 3) == "WIN") ? "\\" : "/";
    $path = realpath(Yii::app()->basePath . $slash . '..' . $slash . '..' .
        $slash . $fileUploadFolder . $slash .
        Helpers::config(Constants::UPLOAD_PATH_CONFIG_DIRECTIVE)) . $slash;
    Yii::app()->params['uploadPath'] = $path;




    parent::init();
  }
}