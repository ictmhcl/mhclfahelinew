<?php


use Firebase\JWT\JWT;

Class H extends Helpers {

}

class Helpers {

  #region Constants
  const DEV_USER_ID = 1;
  const FINANCE_USER_ID = 3;
  const ADMIN_USER_ID = 3;
  const ALLOW_CUSTOM_LIST_MESSAGING = 27;
  #endregion

  #region Dev tools & utils
  public static function runTest() {
    echo Yii::app()->createUrl(Yii::app()->controller->route,$_GET);
  }
  #endregion

  #region English to Dhivehi Name
  /**
   * Generates English-Dhivehi Name pairs from Person table and saves to
   * names_list table with count
   */
  public static function generateDictionary() {
    set_time_limit(5000);
    $sql = "SELECT DISTINCT
              full_name_english, full_name_dhivehi
            FROM persons";
    $personNames = Yii::app()->db->createCommand($sql)->queryAll(false);
    $nameList = [];

    foreach ($personNames as $personName) {
      $engName = trim($personName[0]);
      $divName = trim($personName[1]);
      if (substr_count($engName, " ") == substr_count($divName, " ")) {
        $engNameParts = explode(" ", $engName);
        $divNameParts = explode(" ", $divName);
      }


      foreach ($engNameParts as $k => $namePart) {
        if (!empty($nameList[ucfirst(strtolower($namePart))][$divNameParts[$k]]))
          ++$nameList[ucfirst(strtolower($namePart))][$divNameParts[$k]];
        else
          $nameList[ucfirst(strtolower($namePart))][$divNameParts[$k]] = 1;
      }

    }
    ksort($nameList);
    foreach ($nameList as $engName => $name) {
      foreach ($name as $k => $v) {
        $nameList = new NamesList();
        $nameList->name_english = $engName;
        $nameList->name_dhivehi = $k;
        $nameList->occurance = $v;
        $nameList->save();
      }
    }
    echo "done";

  }

  /**
   * Returns Dhivehi Spelling from a Name given in English
   * @param $engName
   *
   * @return string
   */
  public static function getFullDhivName($engName) {
    $nameParts = explode(" ", trim($engName));
    $divNameParts = [];
    foreach ($nameParts as $namePart)
      $divNameParts[] = self::getDhivName($namePart);

    return self::fixNamesWithAbdul(implode(" ", $divNameParts));
  }

  public static function fixNamesWithAbdul($dhivehiName) {
    $sunLetters = ['ޒ', 'ނ', 'ލ', 'ޡ', 'ޠ', 'ޟ', 'ޞ', 'ޝ', 'ސ', 'ޜ', 'ރ', 'ޛ', 'ދ', 'ޘ', 'ތ'];

    foreach ($sunLetters as $letter) {
      $dhivehiName = str_replace("ޢަބްދުލް " . $letter, "ޢަބްދުއް" . $letter, $dhivehiName);
    }

    return $dhivehiName;
  }

  /**
   * Returns a single Dhivehi spelling from a single name given in English
   * @param $engName
   *
   * @return mixed|string
   */
  public static function getDhivName($engName) {
    if (empty($engName))
      return "";
    $sql = "
      select name_dhivehi from
      (
        select name_dhivehi, occurance
        from names_list
        where name_english = :engName
        order by occurance desc
        limit 1) groupQuery
    ";
    $params = [':engName' => $engName];

    $dhiName = Yii::app()->db->createCommand($sql)->queryScalar($params);

    return empty($dhiName) ? "???" : $dhiName;
  }
  #endregion

  #region Language Translation helper
  /**
   * @param       $category
   * @param       $message
   * @param array $params
   * @param null  $source
   * @param null  $language
   *
   * @return string
   */
  public static function t($category, $message, $params = [], $source = NULL,
    $language = NULL) {

    return Yii::t($category, $message, $params, $source, $language);
  }

  /**
   * @param string $fieldName
   *
   * @return string
   *
   */
  public static function tf($fieldName, $delete = '') {
    if (!empty($delete))
      return str_replace($delete,"",$fieldName);
    if (Yii::app()->language == 'dv') {
      if(strstr($fieldName,'english')!==false)
        return str_replace('english', 'dhivehi', $fieldName);
      elseif(strstr($fieldName,'English') !== false)
        return str_replace('English', 'Dhivehi', $fieldName);

    } elseif (Yii::app()->language == 'en') {
      if (strstr($fieldName, 'dhivehi') !== false)
        return str_replace('dhivehi', 'english', $fieldName);
      elseif (strstr($fieldName, 'Dhivehi') !== false)
        return str_replace('Dhivehi', 'English', $fieldName);
    }
    return $fieldName;
  }

  #endregion

  #region Admin Mobile Number
  /**
   * Returns the mobile number of Admin user
   * @return bool|string
   */
  public static function adminMobile() {
    /**
     * Uncomment below if a admin mobile is of a specific system user
     * Determined as specified in constants
     */

    /** @var Users $user */
//    $user = Users::model()->findByPk(Constants::ADMIN_MOBILE_USER_ID);
//    if (empty($user))
//      return false;
//    else
//      return $user->mobile_number;
    return Constants::ADMIN_MOBILE_NUMBER;
  }
  #endregion

  #region Text Messaging
  /**
   * Sends a text message using Ooredoo Messaging Class
   *
   * @param string|array $recipient
   * @param string $message
   *
   * @return bool
   * @throws \CException
   * @throws \Exception
   */
  public static function textMessage($recipient, $message) {
    
    if (!is_array($recipient))
      $recipient = [$recipient];
    Yii::import('ext.ooredooMessaging.uriduMsg');
    $msg = new uriduMsg();
    $msg->numbers = $recipient;
    $msg->message = $message;
    return $msg->sendMessage();
  }
  #endregion

  #region Check Maldivian Phone numbers
  /**
   * Returns a list of valid Maldivian Mobile phone numbers in a given text
   * @param $textWithNumbers
   *
   * @return array
   */
  public static function pickPhoneNumbers($textWithNumbers) {
    $onlyNumbers = trim(preg_replace("/[^0-9]+/i", " ", $textWithNumbers));

    return array_filter(array_unique(explode(" ", $onlyNumbers)), [__CLASS__, 'validMaldivianPhoneNumber']);
  }

  /**
   * Checks if a number is a valid Maldivian Mobile Telephone Number
   * @param $numberToCheck
   *
   * @return bool
   */
  public static function validMaldivianPhoneNumber($numberToCheck) {
    $firstChar = substr($numberToCheck, 0, 1);
    if (($firstChar == 7 || $firstChar == 9) && strlen(trim($numberToCheck)) == 7)
      return true;

    return false;
  }
  #endregion

  #region Navigation & Menu generation helpers
  /**
   * Returns an array of permitted  navigation items for sub-menu based on
   * current Url or Navigation ID in Url
   *
   * @return array
   */
  public static function sideMenus() {

    // if the Url contains a navigation id, get the navigation record
    $nav = Yii::app()->request->getParam('nav');
    if (!empty($nav)) {
      /** @var Navigation $navItem */
      $navItem = Navigation::model()->with('navigations')->findByPk((int)$nav);

      if (!empty($navItem)) {
        // if the navigation item contains sub menu items and has the same
        // route as this one, select the sub navigation item as the current
        // nav item instead
        $subNavs = $navItem->navigations([
          'condition' => 'app_action_id = ' . $navItem->app_action_id
        ]);

        if (!empty($subNavs)) {
          $navItem = $subNavs[0];
        }
      }
    }

    // If a navigation record could not be found, use the url
    // path to identify current Navigation item
    if (empty($navItem)) {
      /** @var AppActions $appActionItem */
      $appActionItem = AppActions::model()->with('navigations')->find([
        'condition' => 't.controller = :controller AND t.action = :action AND navigations.parent_id IS NOT NULL',
        'params' => [
          'controller' => Yii::app()->controller->id,
          'action' => Yii::app()->controller->action->id,
        ],
      ]);
      // if a matching action item could not be found for current url
      // or there is no navigation for the action found return an empty array
      if (empty($appActionItem) ||
        empty($actionNavItems = $appActionItem->navigations)
      ) {
        return [];
      }

      $navItem = $actionNavItems[0];
    }

    // Get siblings if there is a parent id, otherwise get children
    if (empty($sideNavItems =
      self::menusArray($navItem->parent_id ?: $navItem->id))
    ) {
      return [];
    }

    $sideMenuArray = [];
    foreach ($sideNavItems as $sideNavItem) {
      $sideMenuArray[$sideNavItem->id] = [
        'id' => $sideNavItem->id, 'label' => $sideNavItem->display_text,
        'icon' => $sideNavItem->icon, 'url' => Yii::app()
          ->createUrl($sideNavItem->url, ['nav' => $sideNavItem->id]),
        'active' => (Yii::app()->controller->id . '/' .
          Yii::app()->controller->action->id == $sideNavItem->url),
        'visible' => $sideNavItem->visible
      ];
    }

    return $sideMenuArray;
  }

  /**
   * Returns Navigation Items that the current User has permissions to
   *
   * @param bool|true $visibleOnly
   *
   * @return Navigation[]
   */
  public static function navItems($visibleOnly = true) {

    // Try to get from Cache first
    if (empty(Yii::app()->params['config.navigationItems'])) {
      $criteria = new CDbCriteria();
      if ($visibleOnly) {
        $criteria->compare('visible', '1');
      }
      $criteria->addInCondition('app_action_id', array_keys(self::perms(true)));
//      $criteria->compare('appAction.controller', 'attendance');
      $criteria->with = ['appAction'];
      $criteria->order = '`order` asc';

      /** @var Navigation $navigation */
      $navArray = [];
      // Convert navigation items into a static array (faster after first read)
      foreach (Navigation::model()->findAll($criteria) as $navigation) {
        $attributes = $navigation->attributes;
        $attributes['url'] =
          !empty($navigation->appAction) ? ($navigation->appAction->controller .
            '/' . $navigation->appAction->action) : "";
        $navArray[] = (object)$attributes;
      }
      // Keep it in Yii params cache
      Yii::app()->params['config.navigationItems'] = $navArray;
    }

    return Yii::app()->params['config.navigationItems'];

  }

  /**
   * Returns Navigation Items of a given parent or Admin item
   *
   * @param null       $parent_id
   * @param bool|false $admin
   *
   * @return Navigation[]
   */
  public static function menusArray($parent_id = null, $admin = false) {

    // return navigation items after filtering only the ones with same parent
    // id or is Admin in case admin is true
    return array_filter(self::navItems(), function ($model) use (
      $parent_id, $admin
    ) {
      return ($model->parent_id == $parent_id) &&
      ($admin == ($model->display_text == "Admin"));
    });
  }

  /**
   * Returns Navigation Bar HTML
   *
   * @param null       $id        Parent Item ID. Null means root level
   * @param bool|false $returnHTML Whether this is the top level menu
   * @param bool|false $admin     Whether this the admin menu
   *
   * @return array|string
   */
  public static function menus($id = null, $returnHTML = false,
    $admin = false) {
    $retArray = [];
    foreach (self::menusArray($id, $admin) as $menuItem) {
      $retArray[$menuItem->id] = [
        'id' => $menuItem->id, 'label' => $menuItem->display_text,
        'icon' => $menuItem->icon, 'url' => empty($menuItem->url) ?: Yii::app()
          ->createUrl($menuItem->url, ['nav' => $menuItem->id]),
        'active' => (Yii::app()->controller->id . '/' .
          Yii::app()->controller->action->id == $menuItem->url)
      ];

      // Recursively build the return array if we don't have to return HTML
      if (!$returnHTML) {
        $retArray[$menuItem->id]['items'] = self::menus($menuItem->id);
        // Remove unnecessary item (i.e. no child items or a url link)
        if (empty($retArray[$menuItem->id]['items']) &&
          empty($retArray[$menuItem->id]['url'])
        ) {
          unset($retArray[$menuItem->id]);
        }
      }
    }

    // If we do not have to provide HTML, return plain array
    if (!$returnHTML) {
      return $retArray;
    }

    // Return HTML
    $menuHtml = "";
    foreach ($retArray as $bootMenuItem) {
      $childrenHtml = self::menus($bootMenuItem['id'], true);
      // only display if the menu items has children or a link
      if (!empty($childrenHtml) || !empty($bootMenuItem['url'])) {
        $icon = CHtml::tag('icon', [
          'class' => 'fa fa-' . $bootMenuItem['icon']
        ], '', true);
        $link = CHtml::link($icon . '&nbsp;&nbsp;' .
          $bootMenuItem['label'], $bootMenuItem['url']);
        $menuHtml .= CHtml::tag('li', ['class' => !$bootMenuItem['active'] ?: 'active'], $link .
          $childrenHtml, true);
      }
    }

    // Add a 'ul' wrapper if this is not top menu and contains items in it
    return (!empty($id) &&
      !empty($retArray)) ? Chtml::tag('ul', ['class' => 'dropdown-menu'], $menuHtml) : $menuHtml;

  }
  #endregion

  #region Current User Permissions helper
  /**
   * Returns if the given permission is allowed for the current user
   *
   * @param      $perm
   * @param null $controller
   *
   * @return bool
   */
  public static function hasPerm($perm, $controller = null, $userId = null) {
    return in_array($perm, self::perms(empty($controller),$controller,$userId));
  }

  /**
   * Returns list of permissions for the current user. If allControllers is
   * set to true, returns permissions for all controllers. If allControllers
   * is set to false, then optional third parameter 'controller' may be
   * provided, in which case permissions for the given controller will be
   * returned, otherwise permissions for current controller will be returned.
   * @param bool|false $allControllers
   * @param string|null       $controller
   * @param integer $userId
   *
   * @return array
   */
  public static function perms($allControllers = false, $controller = null,
    $userId = null) {
    $userId = $userId?:Yii::app()->user->id;
    // set controller to current controller if not all controllers or
    // controller is not provided
    if (!$allControllers && empty($controller))
      $controller = Yii::app()->controller->id;

    // return from cache if available
    if ($allControllers && !empty(Yii::app()->params['config.allPerms']))
      return Yii::app()->params['config.allPerms'];
    if (!$allControllers && !empty(Yii::app()->params['config.perms.'.$controller]))
      return Yii::app()->params['config.perms.'.$controller];

    // All permissions allowed for developer, admin user & finance user
    if (in_array($userId, [Helpers::config('devUserId'),
      self::ADMIN_USER_ID, self::FINANCE_USER_ID])) {
      // dev user is allowed all actions (even if not attached to an app
      // function)
      $permList = CHtml::listData(Yii::app()->db->createCommand('SELECT id, action FROM app_actions' .
        ($userId != Helpers::config('devUserId') ?
          ' aa JOIN app_function_actions afa on aa.id = afa.app_action_id' : '') .
        ($allControllers ? "" : (" WHERE controller = '" . $controller . "'")))
          ->queryAll(), 'id', 'action');
    } else {
      $sql = "
              SELECT DISTINCT app_actions.id, app_actions.action
                FROM
                  ((app_function_actions app_function_actions
                    INNER JOIN app_functions app_functions
                      ON (app_function_actions.app_function_id = app_functions.id))
                   INNER JOIN app_actions app_actions
                     ON (app_function_actions.app_action_id = app_actions.id))
                  INNER JOIN user_permissions user_permissions
                    ON (user_permissions.app_function_id = app_functions.id)
                WHERE " .
        ($allControllers ? "" : "(app_actions.controller = :controller) AND ") .
        "(user_permissions.user_id = :user_id)";

      $sql .= "
              UNION";
      $sql .= "
              SELECT DISTINCT app_actions.id, app_actions.action
                FROM (((job_app_functions job_app_functions
                        INNER JOIN app_function_actions app_function_actions
                           ON (job_app_functions.app_function_id =
                                  app_function_actions.app_function_id))
                       INNER JOIN jobs jobs ON (jobs.id = job_app_functions.job_id))
                      INNER JOIN user_jobs user_jobs ON (user_jobs.job_id = jobs.id))
                     INNER JOIN app_actions app_actions
                        ON (app_function_actions.app_action_id = app_actions.id)
               WHERE " .
        ($allControllers ? "" : "(app_actions.controller = :controller) AND ") .
        "(user_jobs.user_id = :user_id) AND user_jobs.approved_datetime IS NOT
         NULL AND user_jobs.cancelled_datetime IS NULL";

      $params = [
        ':user_id' => $userId,
      ];

      if (!$allControllers)
        $params[':controller'] = $controller;

      $permList = CHtml::listData(Yii::app()->db->createCommand($sql)->queryAll(true, $params), 'id', 'action');


    }

    // Cache the results into Yii params
    if ($allControllers)
      Yii::app()->params['config.allPerms'] = $permList;
    else
      Yii::app()->params['config.perms.'.$controller] = $permList;

    return $permList;
  }
  #endregion

  #region Ageega Rates
  public static function getCurrentAgeegaRates() {
    $rates = [];
    $genders = Yii::app()->db->createCommand("
      SELECT gender_id FROM z_gender
    ")->queryColumn();
    foreach ($genders as $gender) {
      $currentRate = Yii::app()->db->createCommand("
        SELECT rate FROM ageega_rates
        WHERE (till_date IS NULL OR till_date >= CURRENT_DATE) AND
          (from_date <= CURRENT_DATE) AND gender_id = :genderId
        ORDER BY from_date DESC
        LIMIT 1;
      ")->bindParam(':genderId', $gender, PDO::PARAM_INT)->queryScalar();
      $rates[$gender] = $currentRate;
    }

    return $rates;
  }
  #endregion

  #region ID resolving using model & attributes
  /**
   * Resolves an id for a given model & attribute
   * @param $model
   * @param $attribute
   *
   * @return string
   */
  public static function resolveID($model, $attribute) {
    return str_replace(']', '', str_replace('[', '_', CHtml::resolveName($model, $attribute)));
  }
  #endregion

  #region Configuration Values Caching & lookup helpers
  /**
   * Caches config values from db into Yii params
   */
  public static function loadConfig() {
    $config = Configuration::model()->findByPk(Constants::MAIN_CONFIG_RECORD_ID);
    foreach ($config->attributeNames() as $attribute) {
      Yii::app()->params['config.' . $attribute] = $config->$attribute;
    }
  }

  /**
   * Fetch a configuration value from configuration table
   *
   * @param string    $paramName configuration Name
   * @param bool|false $reload
   *
   * @return mixed|null
   */
  public static function config($paramName, $reload = false) {

    // If reload is not true try to return from cache if available
    if (isset(Yii::app()->params['config.' . $paramName]) && !$reload)
      return Yii::app()->params['config.' . $paramName];

    // load the config values into cache
    self::loadConfig();

    // Not found
    if (!isset(Yii::app()->params['config.' . $paramName]))
      return null;

    // Found
    return Yii::app()->params['config.' . $paramName];
  }
  #endregion

  #region Currency formatter
  /**
   * Formats a value in comma separated currency format with currency symbols
   * if provided (e.g. MVR 23,570.22)
   * @param float     $value
   * @param null $symbol
   * @param int  $decimals
   *
   * @return string
   */
  public static function currency($value, $symbol = null, $decimals = 2,
    $dir=null) {
    $value = floatval($value);
    $symbol = $symbol ?: H::t('hajj','currencySymbol');
    $dir = $dir ?: H::t('hajj', 'direction');

    return trim(($dir=='ltr'?(trim($symbol) . ' '):'') . number_format($value,
        $decimals, '.',
        ',') . ($dir == 'rtl' ? (' ' . trim($symbol)) : ''));
  }
  #endregion

  #region File Upload & Delete helpers
  /**
   * Returns Url Path for File upload and images folder
   *
   * @param $type
   *
   * @return bool|string
   */
  public static function sysUrl($type) {
    $fileUploadFolder = $GLOBALS['cfg']['fileUploadParentFolder'];
    switch ($type) {
      case (Constants::UPLOADS):
        return Yii::app()->baseUrl . "/../$fileUploadFolder/" . self::config
        (Constants::UPLOAD_PATH_CONFIG_DIRECTIVE) . '/';

      case (Constants::IMAGES):
        return Yii::app()->baseUrl . "/../$fileUploadFolder/" . self::config
        (Constants::IMAGES_PATH_CONFIG_DIRECTIVE) . '/';

      default:
        return false;
    }
  }

  /**
   * Deletes and uploaded file
   * @param $fileName
   *
   * @return bool
   */
  public static function deleteUploadedFile($fileName) {
    try {
      $filePath = Yii::app()->params['uploadPath'] . $fileName;

      if ($filePath && is_readable($filePath)) {
        unlink($filePath);

        return true;
      }

      return false;
    } catch (Exception $ex) {
      return false;
    }
  }
  #endregion

  #region Thaana Date formatting helper
  /**
   * Returns Date formatted in Dhivehi text
   *
   * @param string     $date     Date provided in PHP acceptable format
   * @param bool|false $withHour Returns with hour and minute
   * @param bool|true  $nbsp     Use non breaking spaces
   *
   * @return string
   */
  public static function mvDate($date = null, $withHour = false, $nbsp = true) {
    if (empty($date)) {
      $date = date('Y-m-d');
    }
    $dateParts = getdate(strtotime($date));
    $month = ZDhivehiMonths::model()->findByPk($dateParts['mon'])->name_dhivehi;

    return $dateParts['mday'] . ($nbsp ? '&nbsp;' : ' ') . $month .
    ($nbsp ? '&nbsp;' : ' ') . $dateParts['year'] .
    ($withHour ? (' ' . $dateParts['hours'] . ':' .
      str_pad($dateParts['minutes'], 2, '0', STR_PAD_LEFT)) : '');
  }

  /**
   * Returns dhivehi Month text for given month number
   *
   * @param $month
   *
   * @return string
   * @throws \CHttpException
   */
  public static function mvMonth($month) {
    if ($month < 1 || $month > 12)
      throw new CHttpException(500);
    return ZDhivehiMonths::model()->findByPk($month)->name_dhivehi;
  }
  #endregion

  #region Age calculator, Server Time & time format checking utils
  /**
   * Returns age from a birth date. If atDate is provided, age is calculated
   * at atDate
   *
   * @param string      $bDate Birth date in acceptable PHP date format
   * @param string|null $atDate
   *
   * @return Integer
   */
  public static function age($bDate, $atDate = null) {
    $cmpDate = explode(' ', (new DateTime($atDate))->format('md Y'));
    $birthDate = explode(' ', (new DateTime($bDate))->format('md Y'));

    return $cmpDate[1] - $birthDate[1] - intval($cmpDate[0] < $birthDate[0]);
  }

  /**
   * Returns a json formatted string of present time of Maldives.
   *
   * @param string|null $format Allowed strings are 'datetime',
   *                            'jsdatestring', 'day', 'month', 'year',
   *                            'hour', 'minute', 'second', 'shortdate',
   *                            'localdate', 'timestamp'. If $format is not
   *                            supplied, a JSON array will be returned with
   *                            all of these against respectively named keys.
   *                            Otherwise, a JSON string will be returned.
   *
   * @return string
   */
  public static function serverTime($format = null) {
    date_default_timezone_set(Constants::MALDIVES_TIMEZONE);
    $curTime = time();

    switch ($format) {
      case 'datetime':
        $result = date('D, d M Y H:i:s O', $curTime);
        break;
      case 'jsdatestring':
        $result = date('F d, Y H:i:s', $curTime);
        break;
      case 'day':
        $result = date('d', $curTime);
        break;
      case 'month':
        $result = date('m', $curTime);
        break;
      case 'year':
        $result = date('Y', $curTime);
        break;
      case 'hour':
        $result = date('H', $curTime);
        break;
      case 'minute':
        $result = date('i', $curTime);
        break;
      case 'second':
        $result = date('s', $curTime);
        break;
      case 'shortdate':
        $result = date(Constants::DATE_SAVE_FORMAT, $curTime);
        break;
      case 'localdate':
        $result = date('d/m/Y', $curTime);
        break;
      case 'timestamp':
        $result = $curTime;
        break;
      default:
        $result = [
          'datetime' => date('D, d M Y H:i:s O', $curTime),
          'jsdatestring' => date('F d, Y H:i:s', $curTime),
          'day' => date('d', $curTime),
          'month' => date('m', $curTime),
          'year' => date('Y', $curTime),
          'hour' => date('H', $curTime),
          'minute' => date('i', $curTime),
          'second' => date('s', $curTime),
          'shortdate' => date(Constants::DATE_SAVE_FORMAT, $curTime),
          'localdate' => date('d/m/Y', $curTime),
          'timestamp' => $curTime
        ];
    }

    return CJSON::encode($result);
  }


  /**
   * checks if a string given could be acceptable as a time string.
   * $timeString can be supplied including seconds or without it.
   * $delimiter is the symbol between hour, minute and seconds, which
   * defaults to assume ':'
   *
   * @param        $timeString
   * @param string $delimiter
   *
   * @return bool
   */
  public static function timeCheck($timeString, $delimiter = ':') {

    //3 pieces check => hh:mm:ss
    //2 pieces check => hh:mm

    $timeBits = explode($delimiter, $timeString);

    foreach ($timeBits as $timeBit)
      if (strlen(trim($timeBit)) > 2)
        return false;

    $hour = $timeBits[0];
    $min = $timeBits[1];
    $sec = $timeBits[2] ?: '00';

    if ($hour < 0 || $hour > 23 || !is_numeric($hour))
      return false;

    if ($min < 0 || $min > 59 || !is_numeric($min))
      return false;

    if ($sec < 0 || $sec > 59 || !is_numeric($sec))
      return false;

    return true;
  }
  #endregion

  #region Authentication Helpers
  /**
   * Returns a JSON Web Token with the provided payload
   * @param array $payload
   * @param float $expireDays (default 6 hours i.e. 0.25 days)
   *
   * @return string
   */
  public static function getJwt(array $payload, $expireDays = 0.25) {
    include(Yii::app()->basePath . '/extensions/jwt/JWT.php');

    // Json Token Id: an unique identifier for the token
    $tokenId = base64_encode(mcrypt_create_iv(32));
    $issuedAt = time();
    $serverName = "mv.mhcl.staff";
    $notBefore = $issuedAt + 5;
    $expire = $notBefore + ($expireDays * 24 * 60 * 60);

    $data = [
      'iat' => $issuedAt, 'jti' => $tokenId, 'iss' => $serverName,
      'nbf' => $notBefore, 'exp' => $expire, 'data' => $payload
    ];


    return Firebase\JWT\JWT::encode($data, Yii::app()->params['jwtKey'], 'HS512');
  }
  #endregion

  #region Debugging Stuff
  public static function debugInfo($data = 'No Data', $level = 3) {
    $traces = debug_backtrace();
    $count = 0;
    $msg = "";
    foreach ($traces as $trace) {
      if (++$count > $level)
        break;
      if (isset($trace['file'], $trace['line']) && strpos($trace['file'], YII_PATH) !== 0) {
        $msg .= $trace['file'] . " (" . $trace['line'] . ")\n";
      }
    }

    return $msg . json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
  }

  public static function warning($data = "No Data", $trace = 3) {
    Yii::log(self::debugInfo($data, $trace), CLogger::LEVEL_WARNING, 'application');
  }

  public static function error($data = "No Data", $trace = 3) {
    Yii::log(self::debugInfo($data, $trace), CLogger::LEVEL_ERROR, 'application');
  }

  public static function infoLog($data = "No Data") {
    Yii::log($data, CLogger::LEVEL_INFO, 'application');
  }
  #endregion

  #region String Helpers

  #region Check if string is url encoded
  public static function is_url_encoded($string) {
    $test_string = $string;
    while (urldecode($test_string) != $test_string) {
      $test_string = urldecode($test_string);
    }

    return (urlencode($test_string) == $string) ? true : false;
  }
  #endregion

  #region String reverse utility
  // don't use this with integers or numbers in text
  // only for names
  private static function utf8_strrev($str) {
    preg_match_all('/./us', $str, $ar);

    return join('', array_reverse($ar[0]));
  }
  #endregion

  #endregion

  #region Code Generator
  public static function generateCode($codeLength = 5) {
    return substr(rand(110000, (200000 - 1)) .
      substr(str_pad(rand(110000, (200000 - 1)), 5, '0'), 1), 1, $codeLength);
  }
  #endregion

  #region Response Helpers
  /**
   * Reponds in with JSON Headers
   * @param string $body
   * @param int    $status
   * @param string $content_type
   */
  public static function respondJson($body = null,$status = 200,
    $content_type = 'text/html') {
    $status_header =
      'HTTP/1.1 ' . $status . ' ' . self::_getStatusCodeMessage($status);
    header($status_header);
    header('Content-type: ' . $content_type);
    if (!empty($body)) {
      echo CJSON::encode($body);
    }
    Yii::app()->end();
  }

  /**
   * Returns related Status Message
   * @param $status
   *
   * @return string
   */
  private function _getStatusCodeMessage($status) {
    $codes = [
      200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
      402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found',
      500 => 'Internal Server Error', 501 => 'Not Implemented',
    ];

    return (isset($codes[$status])) ? $codes[$status] : '';
  }
  #endregion



  /**
   * Fetch DNR Records using DNR API in extensions (MHCL Internal Server)
   */

   public static function getDnrRecord($idNo, $name) {

    Yii::import('ext.DnrAPI');
    $api = new DnrAPI();
    
    # sanitize
    $id_card_no = trim($idNo);
    $name = trim($name);
    if (preg_match(Constants::ID_CARD_PATTERN, $id_card_no) != 1) 
      return False;
      // $this->_sendResponse(200, CJSON::encode(['status' => 'failed','Invalid Input for ID: ' . $id_card_no]));
  
    $names = explode(' ', $name);

    foreach ($names as $name) {
      $data = $api->checkDNR($id_card_no, $name);
      if ($data != False)
        return $data;
    }

    // NOTHING FOUND
    return False;

   }

}
