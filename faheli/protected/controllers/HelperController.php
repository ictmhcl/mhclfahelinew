<?php

class HelperController extends Controller {

  #region Default layout Column 2
  public $layout = '//layouts/column2';
  #endregion

  #region Access Control
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
        'actions' => ['generateHajjLists', 'atollIslands', 'dhivehiName' ,'testMessage', 'phoneOtp'],
        'users' => ['*'],
      ], [
        'allow',
        // allow authenticated user to perform 'create' and 'update' actions
        'actions' => [
          'ungroupedMembers', 'addList', 'deleteList', 'getList',
          'ExtractPhoneNumbersFromText', 'mHCMemberNumber', 'mahramDetails',
          'atollIslands', 'deleteUploadedFile', 'person', 'personSearch',
          'umraInfo', 'dhivehiName', 'matureDP', 'organizationBranches',
          'passwordStrength', 'hajjiDetails', 'passportImage',
          'groupsNotAlloted', 'individualsOnFlights', 'allotGroupToFlight',
          'groupPilgrims', 'groupsOnFlight', 'unallotGroupFromFlight',
          'allotIndividualToFlight', 'unallotIndividualFromFlight',
          'flightSeats', 'groupsPendingRoomAllotment', 'groupDetails', 'busGroupDetails',
          'roomTenants', 'individualsPendingRoomAllotment',
          'allotPilgrimsToRoom', 'removeTenant', 'groupsPendingBusAllotment',
          'individualsPendingBusAllotment', 'busPax', 'allotPilgrimsToBus',
          'removePax'
        ], 'users' => ['@'],
      ], [
        'allow',
        // allow authenticated user to perform 'create' and 'update' actions
        'actions' => [
          'runTest', 'helper', 'createAuditLogsFromOpLog',
          'reverseFixTransactionTimes', 'updatePollData', 'exportCards',
          'generateLuggageTags', 'generateIDTags', 'savePassports'
        ], 'users' => ['dev'],
      ], [
        'allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array_merge(Helpers::perms(), ['']), 'users' => ['@'],
      ], [
        'deny', // deny all users
        'users' => ['*'],
      ],
    ];
  }
  #endregion

  #region Flight Allotment APIs
  public function actionUnallotIndividualFromFlight($pilgrimId, $flightId) {
    $deleted = FlightBookings::model()->deleteAllByAttributes([
      'flight_id' => $flightId, 'trip_list_pilgrim_id' => $pilgrimId
    ]);
    if ($deleted > 0) {
      $this->_sendResponse(200, CJSON::encode(['status' => 'success']));
    } else {
      $this->_sendResponse(200, CJSON::encode(['status' => 'failed']));
    }

  }

  public function actionAllotIndividualToFlight($pilgrimId, $flightId) {
    /** @var Flights $flight */
    $flight = Flights::model()->findByPk($flightId);
    if ($flight->available <= 0) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Flight is full'
      ]));
    }
    $flightBooking = new FlightBookings();
    $flightBooking->trip_list_pilgrim_id = $pilgrimId;
    $flightBooking->flight_id = $flightId;
    if ($flightBooking->save()) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'success',
      ]));
    } else {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed',
        'reason' => 'There was an error. Could not add pilgrim to flight',
      ]));
    }
  }

  public function actionFlightSeats($flightId) {
    /** @var Flights $flight */
    $flight = Flights::model()->findByPk($flightId);
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'capacity' => [
        'seats' => $flight->seats, 'alloted' => $flight->alloted,
        'available' => $flight->available
      ]
    ]));
  }
  

  public function actionTestMessage($number = 7774489, $message = 'Mohamed Nazim') {
    Helpers::textMessage($number, $message);
  }

  public function actionGroupsOnFlight($flightId) {
    $sql = "
      select
        tg.id as groupId,
        concat('F',tg.form_number, ' (',
          group_concat(tlp.order_no order by tlp.order_no separator ','),')') as pilgrims
      from flights f
      join flight_bookings fb on f.id = fb.flight_id
      join trip_list_pilgrims tlp on tlp.id = fb.trip_list_pilgrim_id
      join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
      join trip_groups tg on tg.id = tgp.trip_group_id
      join persons p on tlp.person_id = p.id
      where f.id = :flightId
      group by tg.form_number
    ";
    $groups = Yii::app()->db->createCommand($sql)
      ->bindParam(':flightId', $flightId, PDO::PARAM_INT)->queryAll();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'groups' => $groups
    ]));
  }

  public function actionUnallotGroupFromFlight($tripGroupId, $flightId) {
    $sql = "
      DELETE from flight_bookings
      WHERE trip_list_pilgrim_id IN (
        SELECT tgp.trip_list_pilgrim_id FROM trip_group_pilgrims tgp
        WHERE tgp.trip_group_id = :tripGroupId
      ) AND flight_id = :flightId";
    $unallotedPilgrims = Yii::app()->db->createCommand($sql)
      ->bindParam(':tripGroupId', $tripGroupId, PDO::PARAM_INT)
      ->bindParam(':flightId', $flightId, PDO::PARAM_INT)->execute();
    if ($unallotedPilgrims > 0) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'success',
      ]));
    } else {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'No pilgrims were removed'
      ]));
    }
  }

  public function actionAllotGroupToFlight($tripGroupId, $flightId) {
    /** @var Flights $flight */
    $flight = Flights::model()->findByPk($flightId);
    if (empty($flight)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 200, 'reason' => 'Flight not found.'
      ]));
    }
    /** @var Flights $flight */
    if ($flight->available <= 0) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Flight is full'
      ]));
    }
    /** @var TripGroups $group */
    $group =
      TripGroups::model()->with(['tripGroupPilgrims'])->findByPk($tripGroupId);
    if (empty($group)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 200, 'reason' => 'Group not found.'
      ]));
    }

    #region Any group pilgrim already on a flight in the same direction
    // first pilgrim
    $tripListPilgrimId = $group->tripGroupPilgrims[0]->trip_list_pilgrim_id;
    $tripListId = $group->trip_list_id;
    $flightDirection = $flight->departure_flight;
    $currentFlight = Yii::app()->db->createCommand("
        SELECT CONCAT(f.name_english,' (', if(f.departure_flight=1,'departure',
          'return'),')') as flight_name from flight_bookings fb
        JOIN flights f ON f.id = fb.flight_id
        where fb.trip_list_pilgrim_id = :tripListPilgrimId
          AND f.trip_list_id = :tripListId
          AND f.departure_flight = :departureFlight
	    ")->bindParam(':tripListPilgrimId', $tripListPilgrimId, PDO::PARAM_INT)
      ->bindParam(':tripListId', $tripListId, PDO::PARAM_INT)
      ->bindParam(':departureFlight', $flightDirection, PDO::PARAM_INT)
      ->queryScalar();
    if (!empty($currentFlight)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed',
        'reason' => 'Group already alloted to ' . $currentFlight
      ]));
    }
    #endregion

    $dbTransaction = Yii::app()->db->beginTransaction();
    try {
      $flightGroupText = 'F' . $group->form_number . ' (';
      $i = 1;
      foreach ($group->tripGroupPilgrims as $groupPilgrim) {
        $flightBooking = new FlightBookings();
        $flightBooking->flight_id = (int)$flightId;
        $flightBooking->trip_list_pilgrim_id =
          $groupPilgrim->trip_list_pilgrim_id;
        if (!$flightBooking->save()) {
          $this->_sendResponse(200, CJSON::encode([
            'status' => 'failed',
            'reason' => 'Internal Error. Cannot add group to flight'
          ]));
        } else {
          $flightGroupText .= ($i == 1 ? '' : ', ') .
            $groupPilgrim->tripListPilgrim->order_no;
        }
        $i++;
      }
      $flightGroupText .= ')';
      $dbTransaction->commit();
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'success', 'group' => [
          'groupId' => $group->id, 'pilgrims' => $flightGroupText
        ]
      ]));
    } catch (CException $ex) {
      $dbTransaction->rollback();
      ErrorLog::exceptionLog($ex);
      $this->_sendResponse('200', 'Internal Error. We have logged the error' .
        ' and shall try to fix it at the earliest.');
    }


  }

  public function actionGroupsNotAlloted($direction, $tripListId) {
    $sql = "
      select
        tg.id as groupId,
        concat(tg.form_number,' (', count(tg.id),')') as formNo
      from trip_groups tg
      left outer join trip_group_pilgrims tgp on tg.id = tgp.trip_group_id
      left outer join trip_list_pilgrims tlp on tgp.trip_list_pilgrim_id = tlp.id
      left outer join (
          select fbj.trip_list_pilgrim_id, fbj.flight_id from flights fj
          join flight_bookings fbj on fbj.flight_id = fj.id
          where departure_flight = :direction) fb
        on tlp.id = fb.trip_list_pilgrim_id
      where tg.trip_list_id = :tripListId AND fb.flight_id IS NULL
      group by tg.id, tg.form_number
      order by tg.form_number
    ";
    $unalloted = Yii::app()->db->createCommand($sql)
      ->bindParam(':tripListId', $tripListId, PDO::PARAM_INT)
      ->bindParam(':direction', $direction, PDO::PARAM_INT)->queryAll();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'forms' => $unalloted
    ]));
  }

  public function actionGroupPilgrims($groupId) {
    $sql = "
      SELECT
        CONCAT(tlp.order_no, ' - ', p.id_no, ', ', p.full_name_english) as pilgrim
      FROM trip_group_pilgrims tgp
      JOIN trip_list_pilgrims tlp
        ON tlp.id = tgp.trip_list_pilgrim_id
      JOIN persons p
        ON p.id = tlp.person_id
      WHERE tgp.trip_group_id = :groupId
    ";
    $pilgrims = Yii::app()->db->createCommand($sql)
      ->bindParam(':groupId', $groupId, PDO::PARAM_INT)->queryAll();
    $pilgrimsText = '';
    foreach ($pilgrims as $pilgrim) {
      $pilgrimsText .= (empty($pilgrimsText) ? '' : "\n") . $pilgrim["pilgrim"];
    }
    $this->_sendResponse(200, CJSON::encode([
      'pilgrimText' => $pilgrimsText
    ]));
  }

  public function actionIndividualsOnFlights($tripListId, $direction,
    $flightId = null) {
    $flightParam = empty($flightId) ? " IS NULL" : " = $flightId";
    $sql = "
      select
        tlp.id as pilgrimId,
        concat(tlp.order_no,' - ', p.id_no,', ', p.full_name_english) as pilgrim
      from trip_list_pilgrims tlp
      join persons p on p.id = tlp.person_id
      left outer join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
      left outer join (
          select fbj.trip_list_pilgrim_id, fbj.flight_id from flights fj join flight_bookings fbj on fbj.flight_id = fj.id
          where departure_flight = :direction) fb
        on tlp.id = fb.trip_list_pilgrim_id
      where
			tlp.trip_list_id = :tripListId
        AND
			fb.flight_id $flightParam
        AND
		  tgp.id IS NULL
      order by tlp.order_no
    ";
    $params = [':tripListId' => $tripListId, ':direction' => $direction];
    $pilgrims = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'pilgrims' => $pilgrims
    ]));
  }
  #endregion

  #region Room Allotment APIs
  public function actionGroupsPendingRoomAllotment($hotelId) {
    /** @var Hotels $hotel */
    $hotel = Hotels::model()->findByPk($hotelId);
    $triplistId = $hotel->destination->trip_list_id;
    $sql = "
/*      select id, CONCAT(form_number, ': ', group_concat(concat(g, pax) separator ',')) as subs from
      (select tg.id, tg.form_number, tgp.sub_group_number, count(tlp.id) as pax, left(g.name_english,1) as g from trip_list_pilgrims tlp
      join persons p on tlp.person_id = p.id left join z_gender g on p.gender_id = g.gender_id
      left outer join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
      left outer join trip_groups tg on tgp.trip_group_id = tg.id
      left outer join
      (select hrb.trip_list_pilgrim_id from hotel_room_bookings hrb
      join hotel_rooms hr on hr.id = hrb.hotel_room_id
      join hotels h on h.id = hr.hotel_id
      where hotel_id = :hotelId) hrb
      on hrb.trip_list_pilgrim_id = tlp.id
      where tlp.trip_list_id = :tripListId AND hrb.trip_list_pilgrim_id IS NULL
      group by form_number, sub_group_number) groups
      where form_number IS NOT NULL
      group by form_number*/";
    $sql = "
      select
        unalloted_groups.id,
        concat(unalloted_groups.form_number,': ',
          group_concat(group_details.sub_count order by sub_group_number separator ',')) as
          subs
      from
        (select
          tg.id, tg.form_number
        from trip_list_pilgrims tlp
        left outer join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
        left outer join trip_groups tg on tgp.trip_group_id = tg.id
        left outer join
          (select hrb.trip_list_pilgrim_id
          from hotel_room_bookings hrb
          join hotel_rooms hr on hr.id = hrb.hotel_room_id
          join hotels h on h.id = hr.hotel_id
          where hotel_id = :hotelId) hrb
          on hrb.trip_list_pilgrim_id = tlp.id
        where tlp.trip_list_id = :tripListId /*AND hrb.trip_list_pilgrim_id IS NULL*/
        group by tg.id) unalloted_groups
      join
        (select
          tgp.trip_group_id as group_id,
          sub_group_number,
          concat(left(g.name_english,1),count(sub_group_number)) as sub_count
        from trip_group_pilgrims tgp
        join trip_list_pilgrims tlp on tgp.trip_list_pilgrim_id = tlp.id
        left outer join persons p on p.id = tlp.person_id
        left outer join z_gender g on g.gender_id = p.gender_id
        group by tgp.trip_group_id, tgp.sub_group_number
        order by tgp.trip_group_id, tgp.sub_group_number) group_details
        on group_details.group_id = unalloted_groups.id
      group by unalloted_groups.id
      order by unalloted_groups.form_number
    ";
    $unallotedGroups = Yii::app()->db->createCommand($sql)
      ->bindParam(':hotelId', $hotelId, PDO::PARAM_INT)
      ->bindParam(':tripListId', $triplistId, PDO::PARAM_INT)->queryAll();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'groups' => $unallotedGroups
    ]));
  }

  public function actionIndividualsPendingRoomAllotment($hotelId) {
    /** @var Hotels $hotel */
    $hotel = Hotels::model()->findByPk($hotelId);
    $triplistId = $hotel->destination->trip_list_id;
    $sql = "
      select pilgrims.id as pilgrimId,
        concat(pilgrims.order_no, ' - ' , p.id_no, ', ', p.full_name_english,
          ', ', a.abbreviation_english , '. ', i.name_english
        ) as pilgrim, p.gender_id as gender
      from (
        select ungroupedPilgrims.id, ungroupedPilgrims.person_id, ungroupedPilgrims.order_no
        from (
          select tlp.id, tlp.person_id, tlp.order_no, tgp.trip_group_id
          from trip_list_pilgrims tlp
          left outer join trip_group_pilgrims tgp on tlp.id = tgp.trip_list_pilgrim_id
          where trip_list_id = :tripListId AND tgp.id is null) ungroupedPilgrims
        left outer join (
          select rb.trip_list_pilgrim_id from hotel_room_bookings rb
          join hotel_rooms hr on rb.hotel_room_id = hr.id WHERE hr.hotel_id = :hotelId) hrb
          on hrb.trip_list_pilgrim_id = ungroupedPilgrims.id
        where hrb.trip_list_pilgrim_id IS NULL) pilgrims
      join persons p on p.id = pilgrims.person_id
      left outer join z_islands i on i.island_id = p.perm_address_island_id
      left outer join z_atolls a on a.atoll_id = i.atoll_id
      ORDER BY pilgrims.order_no
    ";
    $unallotedIndividuals = Yii::app()->db->createCommand($sql)
      ->bindParam(':hotelId', $hotelId, PDO::PARAM_INT)
      ->bindParam(':tripListId', $triplistId, PDO::PARAM_INT)->queryAll();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'pilgrims' => $unallotedIndividuals
    ]));
  }

  public function actionRoomTenants($roomId) {

    /** @var HotelRooms $room */
    $room = HotelRooms::model()->with(['hotelRoomBookings'])->findByPk($roomId);
    if (empty($room)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Room not found'
      ]));
    }
    $tenants = [];
    foreach ($room->hotelRoomBookings as $hrb) {
      $pilgrim['id'] = $hrb->id;
      $pilgrim['name'] = $hrb->tripListPilgrim->order_no . ' - ' .
        $hrb->tripListPilgrim->person->idName .
        (!empty($hrb->tripListPilgrim->tripGroupPilgrims) ? (" (F" .
          $hrb->tripListPilgrim->tripGroupPilgrims[0]->tripGroup->form_number .
          ")") : "") . " - Age:" . $hrb->tripListPilgrim->person->ageNow .
        ", " . $hrb->tripListPilgrim->person->atollIsland;
      $pilgrim['gender'] = $hrb->tripListPilgrim->person->gender_id;
      $pilgrim['visaOk'] =
        in_array($hrb->tripListPilgrim->order_no, TripGroups::$hatharehFanara);
      $tenants[] = $pilgrim;
    }

    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'tenants' => $tenants
    ]));


  }



  public function actionRemoveTenant() {

    if (empty($_GET['pilgrimIds']) ||
      !is_array($pilgrimIds = $_GET['pilgrimIds'])
    ) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Invalid Request'
      ]));
    }

    /** @var HotelRoomBookings[] $bookings */
    $bookings = HotelRoomBookings::model()->findAllByPk($pilgrimIds);
    $room = $bookings[0]->hotelRoom;
    $deleted = HotelRoomBookings::model()->deleteByPk($pilgrimIds);
    if (!$deleted)
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Could not remove!'
      ]));

    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'room' => [
        'id' => $room->id, 'room_number' => $room->room_number,
        'bed_count' => $room->bed_count, 'taken' => $room->taken,
        'full' => ($room->bed_count - $room->taken == 0 ? 'full' : ''),
        'reserved' => $room->reserved, 'for_sale' => $room->for_sale,
        'gender' => strtolower($room->gender),
      ]
    ]));


  }

  public function actionAllotPilgrimsToRoom() {

    if (empty($_GET['pilgrimIds']) || !is_array($_GET['pilgrimIds']) || empty
      ($_GET['roomId'])
    ) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Invalid Request'
      ]));
    }

    try {
      $pilgrimIds = $_GET['pilgrimIds'];
      $roomId = $_GET['roomId'];
      /** @var HotelRooms $room */
      $room =
        HotelRooms::model()->with(['hotelRoomBookings'])->findByPk($roomId);
      $hotelId = $room->hotel_id;

      // check if pilgrims already has rooms in the same hotel
      $sql = "
        SELECT count(hrb.id) FROM hotel_room_bookings hrb
        JOIN hotel_rooms hr on hr.id = hrb.hotel_room_id
        where hr.hotel_id = :hotelId
        AND hrb.trip_list_pilgrim_id IN (" . implode(',', $pilgrimIds) . ")
      ";
      if (!empty($allotedCount = Yii::app()->db->createCommand($sql)
        ->bindParam(':hotelId', $hotelId, PDO::PARAM_INT)->queryScalar())
      ) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed', 'reason' => $allotedCount . ' pilgrim(s) of
          the selected have already been allotted to rooms'
        ]));
      }

      // check if room has enough room
      if (($available = $room->bed_count - $room->taken) <
        sizeof($pilgrimIds)
      ) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed', 'reason' => "Room " . $room->room_number . " has only $available
          beds left."
        ]));
      }
      $transaction = Yii::app()->db->beginTransaction();
      foreach ($pilgrimIds as $pilgrimId) {
        $booking = new HotelRoomBookings();
        $booking->hotel_room_id = $roomId;
        $booking->trip_list_pilgrim_id = $pilgrimId;
        if (!$booking->save()) {
          $this->_sendResponse(200, CJSON::encode([
            'status' => 'failed', 'reason' => 'Could not allot pilgrims to room. Please contact
            administrator'
          ]));
        }
      }
      $transaction->commit();
      $room->refresh();
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'success', 'room' => [
          'id' => $room->id, 'room_number' => $room->room_number,
          'bed_count' => $room->bed_count, 'taken' => $room->taken,
          'full' => ($room->bed_count - $room->taken == 0 ? 'full' : ''),
          'reserved' => $room->reserved, 'for_sale' => $room->for_sale,
          'gender' => strtolower($room->gender),
        ]
      ]));


    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Internal Server Error'
      ]));
    }


  }

  public function actionGroupDetails($groupId, $hotelId = null) {
    /** @var TripGroups $group */
    $group = TripGroups::model()->with([
      'tripGroupPilgrims' => ['order' => 'sub_group_number']
    ])->findByPk($groupId);
    $pilgrims = [];
    foreach ($group->tripGroupPilgrims as $tgp) {
      $sql = "SELECT count(hrb.id) from hotel_room_bookings hrb join hotel_rooms hr
                on hrb.hotel_room_id = hr.id
              where hr.hotel_id = :hotelId and hrb.trip_list_pilgrim_id =
              {$tgp->trip_list_pilgrim_id}";
      $pilgrim['reserved'] = !empty(Yii::app()->db->createCommand($sql)
        ->bindParam(':hotelId', $hotelId, PDO::PARAM_INT)->queryScalar());
      $pilgrim['id'] = $tgp->trip_list_pilgrim_id;
      $pilgrim['name'] = $tgp->tripListPilgrim->order_no . ' - ' .
        $tgp->tripListPilgrim->person->full_name_english . ', ' .
        $tgp->tripListPilgrim->person->ageNow . ', ' .
        $tgp->tripListPilgrim->person->atollIsland . (!empty
        ($tgp->tripListPilgrim->hotelRoomBookings) ? ("-" .
          $tgp->tripListPilgrim->hotelRoomBookings[0]->hotelRoom->room_number) : '');
      $pilgrim['gender'] = $tgp->tripListPilgrim->person->gender_id;
      $pilgrim['subForm'] = $tgp->sub_group_number;
      $pilgrim['visaOk'] =
        in_array($tgp->tripListPilgrim->order_no, TripGroups::$hatharehFanara);
      $pilgrims[] = $pilgrim;
    }

    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success',
      'pilgrims' => array_msort($pilgrims, ['subForm' => SORT_ASC])
    ]));


  }
  #endregion

  #region Bus Allotment APIs

  public function actionBusGroupDetails($groupId, $routeId = null) {
    /** @var TripGroups $group */
    $group = TripGroups::model()->with([
      'tripGroupPilgrims' => ['order' => 'sub_group_number']
    ])->findByPk($groupId);
    $pilgrims = [];
    foreach ($group->tripGroupPilgrims as $tgp) {
      $sql = "SELECT count(bp.id) from bus_pilgrims bp join buses b
                on bp.bus_id = b.id
              where b.route_id = :routeId and bp.trip_list_pilgrim_id =
              {$tgp->trip_list_pilgrim_id}";
      $pilgrim['reserved'] = !empty(Yii::app()->db->createCommand($sql)
        ->bindParam(':routeId', $routeId, PDO::PARAM_INT)->queryScalar());
      $pilgrim['id'] = $tgp->trip_list_pilgrim_id;
      $pilgrim['name'] = $tgp->tripListPilgrim->order_no . ' - ' .
        $tgp->tripListPilgrim->person->full_name_english . ', ' .
        $tgp->tripListPilgrim->person->ageNow . ', ' .
        $tgp->tripListPilgrim->person->atollIsland . (!empty
        ($tgp->tripListPilgrim->busPilgrims) ? ("-B" .
          $tgp->tripListPilgrim->busPilgrims[0]->bus->bus_number) : '') . (!empty
        ($tgp->tripListPilgrim->flightBookings) ? ("-F" .
          $tgp->tripListPilgrim->flightBookings[0]->flight->id) : '');
      $pilgrim['gender'] = $tgp->tripListPilgrim->person->gender_id;
      $pilgrim['subForm'] = $tgp->sub_group_number;
      $pilgrim['visaOk'] =
        in_array($tgp->tripListPilgrim->order_no, TripGroups::$hatharehFanara);
      $pilgrims[] = $pilgrim;
    }

    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success',
      'pilgrims' => array_msort($pilgrims, ['subForm' => SORT_ASC])
    ]));


  }


  public function actionIndividualsPendingBusAllotment($routeId) {
    /** @var Routes $route */
    $route = Routes::model()->findByPk($routeId);
    $triplistId = $route->trip_list_id;
    $sql = "
      select pilgrims.id as pilgrimId
/*      ,
        concat(pilgrims.order_no, ' - ' , p.id_no, ', ', p.full_name_english,
          ', ', a.abbreviation_english , '. ', i.name_english
        ) as pilgrim, p.gender_id as gender */
      from (
        select ungroupedPilgrims.id, ungroupedPilgrims.person_id, ungroupedPilgrims.order_no
        from (
          select tlp.id, tlp.person_id, tlp.order_no, tgp.trip_group_id
          from trip_list_pilgrims tlp
          left outer join trip_group_pilgrims tgp on tlp.id = tgp.trip_list_pilgrim_id
          where trip_list_id = :tripListId AND tgp.id is null) ungroupedPilgrims
        left outer join (
          select bp.trip_list_pilgrim_id from bus_pilgrims bp
          join buses b on bp.bus_id = b.id WHERE b.route_id = :routeId)
           bps
          on bps.trip_list_pilgrim_id = ungroupedPilgrims.id
        where bps.trip_list_pilgrim_id IS NULL) pilgrims
      join persons p on p.id = pilgrims.person_id
      left outer join z_islands i on i.island_id = p.perm_address_island_id
      left outer join z_atolls a on a.atoll_id = i.atoll_id
      ORDER BY pilgrims.order_no
    ";
    $unallotedIndividuals = Yii::app()->db->createCommand($sql)
      ->bindParam(':routeId', $routeId, PDO::PARAM_INT)
      ->bindParam(':tripListId', $triplistId, PDO::PARAM_INT)->queryColumn();
    $criteria = new CDbCriteria();
    $criteria->addInCondition('id', $unallotedIndividuals);
    /** @var TripListPilgrims[] $pilgrims */
    $pilgrims = TripListPilgrims::model()->findAll($criteria);
    $unallotedPilgrims = [];
    foreach ($pilgrims as $pilgrim) {
      $x['id'] = $pilgrim->id;
      $x['pilgrim'] = $pilgrim->order_no . ', ' . $pilgrim->person->idName .
        ', ' . $pilgrim->person->atollIsland;
      $x['gender'] = $pilgrim->person->gender_id;
      $x['visaOk'] = in_array($pilgrim->order_no, TripGroups::$hatharehFanara);
      $unallotedPilgrims[] = $x;
    }
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'pilgrims' => $unallotedPilgrims
    ]));
  }

  public function actionGroupsPendingBusAllotment($routeId) {
    /** @var Routes $route */
    $route = Routes::model()->findByPk($routeId);
    $triplistId = $route->trip_list_id;
    $sql = "
/*      select id, CONCAT(form_number, ': ', group_concat(concat(g, pax) separator ',')) as subs from
      (select tg.id, tg.form_number, tgp.sub_group_number, count(tlp.id) as pax, left(g.name_english,1) as g from trip_list_pilgrims tlp
      join persons p on tlp.person_id = p.id left join z_gender g on p.gender_id = g.gender_id
      left outer join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
      left outer join trip_groups tg on tgp.trip_group_id = tg.id
      left outer join
      (select hrb.trip_list_pilgrim_id from hotel_room_bookings hrb
      join hotel_rooms hr on hr.id = hrb.hotel_room_id
      join hotels h on h.id = hr.hotel_id
      where hotel_id = :hotelId) hrb
      on hrb.trip_list_pilgrim_id = tlp.id
      where tlp.trip_list_id = :tripListId AND hrb.trip_list_pilgrim_id IS NULL
      group by form_number, sub_group_number) groups
      where form_number IS NOT NULL
      group by form_number*/";
    $sql = "
      select
        unalloted_groups.id,
        concat(unalloted_groups.form_number,': ',
          group_concat(group_details.sub_count order by sub_group_number separator ',')) as
          subs
      from
        (select
          tg.id, tg.form_number
        from trip_list_pilgrims tlp
        left outer join trip_group_pilgrims tgp on tgp.trip_list_pilgrim_id = tlp.id
        left outer join trip_groups tg on tgp.trip_group_id = tg.id
        left outer join
          (select bp.trip_list_pilgrim_id
          from bus_pilgrims bp
          join buses b on b.id = bp.bus_id
          join routes r on r.id = b.route_id
          where route_id = :routeId) bps
          on bps.trip_list_pilgrim_id = tlp.id
        where tlp.trip_list_id = :tripListId /*AND hrb.trip_list_pilgrim_id IS NULL*/
        group by tg.id) unalloted_groups
      join
        (select
          tgp.trip_group_id as group_id,
          sub_group_number,
          concat(left(g.name_english,1),count(sub_group_number)) as sub_count
        from trip_group_pilgrims tgp
        join trip_list_pilgrims tlp on tgp.trip_list_pilgrim_id = tlp.id
        left outer join persons p on p.id = tlp.person_id
        left outer join z_gender g on g.gender_id = p.gender_id
        group by tgp.trip_group_id, tgp.sub_group_number
        order by tgp.trip_group_id, tgp.sub_group_number) group_details
        on group_details.group_id = unalloted_groups.id
      group by unalloted_groups.id
      order by unalloted_groups.form_number
    ";
    $unallotedGroups = Yii::app()->db->createCommand($sql)
      ->bindParam(':routeId', $routeId, PDO::PARAM_INT)
      ->bindParam(':tripListId', $triplistId, PDO::PARAM_INT)->queryAll();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'groups' => $unallotedGroups
    ]));
  }

  public function actionBusPax($busId) {
    /** @var Buses $bus */
    $bus = Buses::model()->with(['busPilgrims'])->findByPk($busId);
    if (empty($bus)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Bus not found'
      ]));
    }
    $tenants = [];
    foreach ($bus->busPilgrims as $busPilgrim) {
      $pilgrim['id'] = $busPilgrim->id;
      $pilgrim['name'] = $busPilgrim->pilgrim->order_no . ' - ' .
        $busPilgrim->pilgrim->person->idName .
        (!empty($busPilgrim->pilgrim->tripGroupPilgrims) ? (" (F" .
          $busPilgrim->pilgrim->tripGroupPilgrims[0]->tripGroup->form_number .
          ")") : "") . " - Age:" . $busPilgrim->pilgrim->person->ageNow . ", " .
        $busPilgrim->pilgrim->person->atollIsland;
      $pilgrim['gender'] = $busPilgrim->pilgrim->person->gender_id;
      $pilgrim['visaOk'] =
        in_array($busPilgrim->pilgrim->order_no, TripGroups::$hatharehFanara);
      $tenants[] = $pilgrim;
    }

    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'pax' => $tenants
    ]));


  }

  public function actionRemovePax() {

    if (empty($_GET['pilgrimIds']) || !is_array($pilgrimIds = $_GET['pilgrimIds'])) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Invalid Request'
      ]));
    }

    /** @var BusPilgrims $bp */
    $bps = BusPilgrims::model()->findAllByPk($pilgrimIds);
    $bus = $bps[0]->bus;
    $transaction = Yii::app()->db->beginTransaction();
    foreach ($bps as $bp) {
      $deleted = $bp->delete();
      if (!$deleted) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed', 'reason' => 'Could not remove!'
        ]));
        $transaction->rollback();
        Yii::app()->end();
      }
    }
    $transaction->commit();
    $this->_sendResponse(200, CJSON::encode([
      'status' => 'success', 'bus' => [
        'id' => $bus->id, 'bus_number' => $bus->bus_number,
        'seats' => $bus->seats, 'taken' => $bus->taken,
        'full' => ($bus->seats - $bus->taken == 0 ? 'full' : ''),
      ]
    ]));

  }

  public function actionAllotPilgrimsToBus() {

    if (empty($_GET['pilgrimIds']) || !is_array($_GET['pilgrimIds']) || empty
      ($_GET['busId'])
    ) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Invalid Request'
      ]));
    }

    try {
      $pilgrimIds = $_GET['pilgrimIds'];
      $busId = $_GET['busId'];
      /** @var Buses $bus */
      $bus = Buses::model()->with(['busPilgrims'])->findByPk($busId);
      $routeId = $bus->route_id;

      // check if pilgrims already has rooms in the same hotel
      $sql = "
        SELECT count(bp.id) FROM bus_pilgrims bp
        JOIN buses b on b.id = bp.bus_id
        where b.route_id = :routeId
        AND bp.trip_list_pilgrim_id IN (" . implode(',', $pilgrimIds) . ")
      ";
      if (!empty($allotedCount = Yii::app()->db->createCommand($sql)
        ->bindParam(':routeId', $routeId, PDO::PARAM_INT)->queryScalar())
      ) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed', 'reason' => $allotedCount . ' pilgrim(s) of
          the selected have already been allotted to buses'
        ]));
      }

      // check if room has enough room
      if (($available = $bus->seats - $bus->taken) < sizeof($pilgrimIds)) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed', 'reason' => "Bus Number " . $bus->bus_number . "
           has only $available seats left."
        ]));
      }
      $transaction = Yii::app()->db->beginTransaction();
      foreach ($pilgrimIds as $pilgrimId) {
        $bp = new BusPilgrims();
        $bp->bus_id = $busId;
        $bp->trip_list_pilgrim_id = $pilgrimId;
        if (!$bp->save()) {
          $this->_sendResponse(200, CJSON::encode([
            'status' => 'failed', 'reason' => 'Could not allot pilgrims to bus.
             Please contact administrator'
          ]));
        }
      }
      $transaction->commit();
      $bus->refresh();
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'success', 'bus' => [
          'id' => $bus->id, 'bus_number' => $bus->bus_number,
          'seats' => $bus->seats, 'taken' => $bus->taken,
          'full' => ($bus->seats - $bus->taken == 0 ? 'full' : ''),
        ]
      ]));


    } catch (CException $ex) {
      ErrorLog::exceptionLog($ex);
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Internal Server Error'
      ]));
    }


  }


  #endregionƒa

  public function actionPhoneOtp($number) {
    $code = Helpers::generateCode(6);
    $codeSent = Helpers::textMessage($number, 'MHCL Faheli OTP: '.$code);
    if ($codeSent) {
      if (!empty($phoneOtp = PhoneSmsCodes::model()->findByAttributes(['phone' => $number]))) {
        $phoneOtp->code = $code;
        $phoneOtp->save(false);
      } else {
        $phoneOtp = new PhoneSmsCodes();
        $phoneOtp->phone = $number;
        $phoneOtp->code = $code;
        $phoneOtp->save(false);
      }
      $this->_sendResponse(200, CJSON::encode(['status' => 'success', 'message'=> H::t('site','otpSent')]));
    }
    $this->_sendResponse(200, CJSON::encode(['status' => 'failed', 'message'=> H::t('site', 'cannotSendOtp')]));

  }

  #region Helper APIs (dhivehiName, passwordStrength, hajjiDetails)

  public function actionDhivehiName($q) {
    $this->_sendResponse(200, CJSON::encode(['dhivehiName' => Helpers::getFullDhivName($q)]));
  }

  


  public function actionPasswordStrength($p) {
    /** @var Users $curUser */
    $curUser = Users::model()->findByPk(Yii::app()->user->id);
    $user = new UserUpdate();
    $user->user_name = $curUser->user_name;
    $user->password = $p;
    $user->validate(['password'], true);
    if ($user->hasErrors('password')) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'error' => $user->getError('password')
      ]));
    } else {
      $this->_sendResponse(200, CJSON::encode(['status' => 'success']));
    }
  }

  public function actionHajjiDetails($q, $tripListId) {
    $criteria = new CDbCriteria();
    $criteria->condition = '(person.id_no = :query or order_no = :query) AND
    trip_list_id = :tripListId';
    $criteria->with = ['person'];
    $criteria->params = [':query' => $q, ':tripListId' => $tripListId];
    /** @var TripListPilgrims $pilgrim */
    $pilgrim = TripListPilgrims::model()->find($criteria);
    if (empty($pilgrim)) {
      $this->_sendResponse(200, CJSON::encode([
        'status' => 'failed', 'reason' => 'Hajji No or ID Not found.'
      ]));
    } else /** @var TripGroupPilgrims $tripGroupPilgrim */ {
      if (!empty($tripGroupPilgrim =
        TripGroupPilgrims::model()->findByAttributes([
          'trip_list_pilgrim_id' => $pilgrim->id
        ]))
      ) {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'failed',
          'reason' => 'Hajji "' . $pilgrim->person->idName . '" already
          entered on Form No. ' . $tripGroupPilgrim->tripGroup->form_number
        ]));

      } else {
        $this->_sendResponse(200, CJSON::encode([
          'status' => 'success', 'idNo' => $pilgrim->person->id_no,
          'hajjiNo' => $pilgrim->order_no, 'tripListPilgrimId' => $pilgrim->id,
          'idName' => $pilgrim->person->idName,
          'gender' => $pilgrim->person->gender->name_english,
          'island' => $pilgrim->person->atollIsland
        ]));
      }
    }
  }

  #endregion

  #region Passport image actions

  public function actionSavePassports($id = 2) {
    ini_set('max_execution_time', 6000);
    $criteria = new CDbCriteria();
    $criteria->condition = 'trip_list_id = ' . (int)$id;
    $criteria->order = 'order_no asc';
    /** @var TripListPilgrims[] $pilgrims */
    $pilgrims = TripListPilgrims::model()->with(['person'])->findAll($criteria);
    foreach ($pilgrims as $pilgrim) {
      //      echo ($pilgrim->person->id_no);
      $this->savePassportImage($pilgrim->person->id_no, 1200, true);
    }
  }

  public function actionPassports($id = 2) {
    $this->layout = "//layouts/print";
    $fromCondition = !empty($from) ? ' AND order_no >=' . (int)$from : '';
    $tillCondition = !empty($till) ? ' AND order_no <=' . (int)$till : '';
    $criteria = new CDbCriteria();
    $criteria->condition =
      'trip_list_id = ' . (int)$id . $fromCondition . $tillCondition;
    $criteria->order = 'order_no asc';
    $pilgrims = TripListPilgrims::model()->with(['person'])->findAll($criteria);
    $this->render('/lists/passportPrints', [
      'pilgrims' => $pilgrims, 'trip' => TripLists::model()->findByPk($id)
    ]);
  }

  public function actionPassportImage($idNo, $width = 1200, $save = false) {
    $height = (int)$width / 2;
    /** @var Persons $person */
    $person = Persons::model()->findByAttributes([
      'id_no' => trim($idNo)
    ]);
    //todo: error handling for unfound person
    $passport = $person->latestPassport;
    $fileName =
      'https://mhclonline.com:19443/hajjWS/web/files/' . $passport->pp_copy;

    $part = pathinfo($fileName);
    $ext = strtolower($part['extension']);
    if ($ext == "jpg" or $ext == "jpeg") {
      $fileResource = imagecreatefromjpeg($fileName);
    }
    if ($ext == "gif") {
      $fileResource = imagecreatefromgif($fileName);
    }
    if ($ext == "png") {
      $fileResource = imagecreatefrompng($fileName);
    }

    //todo: error handling if file not found

    // crop
    $croppedResource = imagecrop($fileResource, [
      'x' => 100, 'y' => 132, 'width' => $x = 1800, 'height' => $y = 900
    ]);
    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $croppedResource, 0, 0, 0, 0, $width, $height, $x, $y);

    imagefilter($resizedImage, IMG_FILTER_BRIGHTNESS, 100);

    header('Content-Type: image/jpeg');
    if ($save) {
      imagejpeg($resizedImage, Yii::app()->params['uploadPath'] . 'passports/' .
        $person->id_no . '.jpeg');
    } else {
      imagejpeg($resizedImage, null, 40);
    }

  }

  public function savePassportImage($idNo, $width = 1200, $save = false) {

    $height = (int)$width / 2;
    /** @var Persons $person */
    $person = Persons::model()->findByAttributes([
      'id_no' => trim($idNo)
    ]);
    //todo: error handling for unfound person
    if (!empty($person->latestPassport)) {
      $passport = $person->latestPassport;
      $fileName =
        'https://mhclonline.com:19443/hajjWS/web/files/' . $passport->pp_copy;
      copy($fileName, Yii::app()->params['uploadPath'] . 'passports/' .
        $person->id_no . '.jpeg');
      //      $part = pathinfo($fileName);
      //      $ext = strtolower($part['extension']);
      //      if ($ext == "jpg" or $ext == "jpeg") {
      //        $fileResource = imagecreatefromjpeg($fileName);
      //      }
      //      if ($ext == "gif") {
      //        $fileResource = imagecreatefromgif($fileName);
      //      }
      //      if ($ext == "png") {
      //        $fileResource = imagecreatefrompng($fileName);
      //      }
      //
      //      //todo: error handling if file not found
      //
      //      // crop
      //      $croppedResource = imagecrop($fileResource, [
      //        'x' => 100,
      //        'y' => 132,
      //        'width' => $x = 1800,
      //        'height' => $y = 900
      //      ]);
      //      $resizedImage = imagecreatetruecolor($width, $height);
      //      imagecopyresampled($resizedImage, $croppedResource, 0, 0, 0, 0, $width,
      //        $height, $x, $y);
      //
      //      imagefilter($resizedImage, IMG_FILTER_BRIGHTNESS, 100);
      //
      //      header('Content-Type: image/jpeg');
      //      if ($save) {
      //        imagejpeg($resizedImage, Yii::app()->params['uploadPath'] .
      //          'passports/' . $person->id_no . '.jpeg');
      //      } else {
      //        imagejpeg($resizedImage, null, 40);
      //      }
    }

  }

  #endregion


  public function actionGenerateHajjLists() {
    Helpers::generateHajjLists();
    Helpers::uploadHajjLists();
  }

  public function actionUmraInfo($id) {
    /** @var UmraTrips $umraTrip */
    $umraTrip = UmraTrips::model()->findByPk($id);
    if (!$umraTrip) {
      $this->_sendResponse(404, CJSON::encode([]));
    }

    $departureDate = empty($umraTrip->departure_date) ? $umraTrip->year . '/' .
      $umraTrip->month .
      '/01' : (new DateTime($umraTrip->departure_date))->format('Y/m/d');

    $malesList = [0 => "Select Mahram"];
    $malesOnThisTrip = UmraPilgrims::model()->with('person')->findAll('person.gender_id = :males AND
                  umra_trip_id = :umraTripId
                  ', [
      ':males' => Constants::GENDER_MALE, ':umraTripId' => $umraTrip->id,
    ]);
    foreach ($malesOnThisTrip as $malePilgrim) {
      $malesList[$malePilgrim->id] = $malePilgrim->person->id_no . ' - ' .
        $malePilgrim->person->full_name_english;
    }

    $groupNames = Yii::app()->db->createCommand("
            SELECT group_name
            FROM umra_pilgrims
            WHERE umra_trip_id = :umraTripId AND group_name IS NOT NULL
            GROUP BY group_name
            ")->queryColumn([':umraTripId' => $umraTrip->id]);
    $groupNameList = [];
    foreach ($groupNames as $groupName) {
      $groupNameList[$groupName] = $groupName;
    }
    $groupNameList["zzzzz"] = 'Other...';


    $this->_sendResponse(200, CJSON::encode([
      'departureDate' => $departureDate, 'availableMahrams' => $malesList,
      'groupNames' => $groupNameList,
    ]));
  }

  public function actionPersonSearch($q, $type = "person") {
    $criteria = new CDbCriteria();
    $criteria->compare('id_no', $q, true);
    $criteria->compare('full_name_english', $q, true, 'OR');

    if ($type == "member") {
      $criteria->with = ['member'];
      $criteria->compare('member.mhc_no', $q, false, 'OR');
      $criteria->addCondition('member.id IS NOT NULL');
    }
    if ($type == "user") {
      $criteria->with = ['user'];
      $criteria->compare('user.user_name', $q, false, 'OR');
      $criteria->addCondition('user.id IS NOT NULL');
    }
    $persons = Persons::model()->findAll($criteria);
    $personsArray = [];
    foreach ($persons as $person) {
      $x = [];
      $x['id'] = $person->id;
      $x['text'] = $person->idName . ($type=='person'?", " .
      $person->country->name:"");
      $personsArray[] = $x;
    }
    //CVarDumper::dump($personsArray);
    $this->_sendResponse(200, CJSON::encode(['items' => $personsArray]));
  }

  public function actionPerson($id) {
    $person = Persons::model()->findByPk($id);
    if (is_null($person)) {
      $this->_sendResponse(200, CJSON::encode([]));
    } else {
      $this->_sendResponse(200, CJSON::encode([
        'id' => $person->id, 'text' => $person->idName
      ]));
    }
  }

  private function _sendResponse($status = 200, $body = '',
    $content_type = 'text/html') {
    $status_header =
      'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
    header($status_header);
    header('Content-type: ' . $content_type);
    if ($body != '') {
      echo $body;
    }
    Yii::app()->end();
  }

  //  public function actionGenerateLuggageTags() {
  //
  //    $cardRows = 3;
  //    $cardColumns = 3;
  //    $cardWidth = 86.36;
  //    $cardHeight = 52.74;
  //    $top = 26.66;
  //    $left = 19.57;
  //
  //    $newTagCommon = new stdClass();
  //    $newTagCommon->year = "2015";
  //    $newTagCommon->yearArabic = "1436";
  //    $newTagCommon->phone1 = "+966 2 5739962";
  //    $newTagCommon->phone2 = "+966 2 5739974";
  //    $newTagCommon->phone3 = "+966 2 5702529";
  //    $newTagCommon->phone4 = "+966 2 5351290";
  //    $newTagCommon->hotline = "+960 333333";
  //    $newTagCommon->hotelDhivehi = "ރައިޙާނާ ހޮޓާ";
  //    $newTagCommon->hotelArabic = "الريحانة :فندق";
  //    $newTagCommon->hotelEnglish = "Hotel Al-Rayhana";
  //
  //    $tags = Tags::model()->findAll(['order' => 'id desc']);
  //
  //    include(Yii::app()->basePath . '/extensions/tcpdf/tcpdf.php');
  //    include(Yii::app()->basePath . '/extensions/fpdi/fpdi.php');
  //    $pdfFile = Yii::app()->basePath . "/../tags/Luggage_Card_3x3.pdf";
  //
  //    $pdf = new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,
  //      'UTF-8', false);
  //
  //    // Get Template
  //    $pageCount = $pdf->setSourceFile($pdfFile);
  //    $tplIdx = $pdf->importPage(1);
  //
  //    $pdf->SetTextColor(0, 0, 0);
  //
  //    $fonts['Arial'] = TCPDF_FONTS::addTTFfont
  //    (Yii::app()->basePath . '/extensions/tcpdf/fonts/arial.ttf',
  //      'TrueTypeUnicode', '', 32);
  //    $fonts['Faruma'] = TCPDF_FONTS::addTTFfont
  //    (Yii::app()->basePath . '/extensions/tcpdf/fonts/faruma.ttf',
  //      'TrueTypeUnicode', '', 32);
  //
  //    $tagCount = 0;
  //    foreach ($tags as $tag) {
  //      if ($tagCount % ($cardColumns * $cardRows) == 0) {
  //        $pdf->AddPage('L', 'A4');
  //        $pdf->useTemplate($tplIdx, 0, 0);
  //      }
  //      $horizontalOffset = $left + ($tagCount % $cardColumns * $cardWidth);
  //      $verticalOffset = $top + ((((int)($tagCount / $cardRows)) % $cardRows) *
  //          $cardHeight);
  //
  //      $this->_generateOneTag($pdf, $newTagCommon, $tag, $fonts, $horizontalOffset,
  //        $verticalOffset);
  //
  //      ++$tagCount;
  //    }
  //
  //    $pdf->Output();
  //
  //  }
  //
  //  private function _generateOneTag(&$pdf, $tagCommon, $tag, $fonts, $xOff,
  //                                   $yOff) {
  //
  //    // id
  //    $pdf->setXY($xOff + 35.90, $yOff + 13);
  //    $pdf->setFont('helvetica', 'B', 9.7);
  //    $pdf->Cell(30, 5, str_pad($tag->id, 3, '0', STR_PAD_LEFT), 0, 1, 'R');
  //
  ////     name original
  ////    $pdf->setXY($xOff + 35.90, $yOff + 17.35);
  ////    $pdf->setFont('helvetica', '', 7.32);
  ////    $pdf->Cell(30, 5, $tag->name, 0, 1, 'R');
  //
  //    // name
  //    $pdf->setXY($xOff + 35.90, $yOff + 17.35);
  //    $pdf->setTextColor(255, 0, 0);
  //    $pdf->setFont('helvetica', '', 10.32);
  //    $pdf->Cell(30, 5, strtoupper($tag->name), 0, 1, 'R');
  //
  //    $pdf->setTextColor(0, 0, 0);
  //
  //    // arabic name
  //    $pdf->setXY($xOff + 35.90, $yOff + 21.65);
  //    $pdf->setFont($fonts['Arial'], '', 11.21);
  //    $pdf->Cell(22.2, 5, $tag->nameArabic, 0, 1, 'R');
  //
  //    // pp no
  //    $pdf->setXY($xOff + 35.90, $yOff + 27.25);
  //    $pdf->setFont('helvetica', '', 6.47);
  //    $pdf->Cell(12, 5, $tag->passportNo, 0, 1, 'R');
  //
  //    // phone numbers
  //    for ($i = 1; $i <= 4; $i++) {
  //      $pdf->setXY($xOff + 35.90, $yOff + 34.55 + (2.68 * ($i - 1)));
  //      $pdf->setFont('helvetica', '', 4.98);
  //      $pdf->Cell(5.6, 5, $tagCommon->{'phone' . $i}, 0, 1, 'R');
  //    }
  //
  //    // hotline
  //    $pdf->setXY($xOff + 51.90, $yOff + 46.45);
  //    $pdf->setFont('helvetica', 'B', 5.53);
  //    $pdf->Cell(6.1, 5, $tagCommon->hotline, 0, 1);
  //
  //    // hotel Dhivehi
  //    $pdf->setXY($xOff + 51.90, $yOff + 35.25);
  //    $pdf->setFont('Faruma', 'B', 7.33);
  //    $pdf->Cell(31, 5, $tagCommon->hotelDhivehi, 0, 1, 'R');
  //
  //    // hotel Arabic
  //    $pdf->setXY($xOff + 51.90, $yOff + 38.45);
  //    $pdf->setFont($fonts['Arial'], '', 7.59);
  //    $pdf->Cell(31, 5, $tagCommon->hotelArabic, 0, 1, 'R');
  //
  //    // Hotel English
  //    $pdf->setXY($xOff + 51.90, $yOff + 42.15);
  //    $pdf->setFont('helvetica', '', 5.84);
  //    $pdf->Cell(31, 5, $tagCommon->hotelEnglish, 0, 1, 'R');
  //
  //    // image
  //    $member = Members::model()->findByPk($tag->id);
  //    $imagePath = Yii::app()->params['uploadPath'] .
  //      $member->registrationForm->passport_photo;
  //    $pdf->setXY($xOff + 51.90, $yOff + 22.15);
  //    $pdf->Image($imagePath, $xOff + 68.65, $yOff + 13.75, 13.80, 0, '', true,
  //      300);
  //
  //    $pdf->setTextColor(255, 255, 255);
  //
  //    $pdf->setXY($xOff + 2.90, $yOff + 42.45);
  //    $pdf->setFont($fonts['Arial'], '', 8.21);
  //    $pdf->Cell(10, 5, '۱۴۳۶', 0, 1, 'L');
  //    $pdf->setTextColor(0, 0, 0);
  //  }
  //

  public function actionGenerateIDBackTags() {

    ini_set('memory_limit', '2048M');

    $cardRows = 2;
    $cardColumns = 4;
    $cardWidth = 55.623;
    $cardHeight = 94.565;
    $top = 5.21;
    $left = -3 + 55.623;

    $tags = Hajj1436::model()->findAll([
      'order' => 'hajji_no asc', //      'limit' => 10,
      //      'offset' => 150,
      //    'condition' => 'hajji_no > 150'
    ]);

    include(Yii::app()->basePath . '/extensions/tcpdf/tcpdf.php');
    include(Yii::app()->basePath . '/extensions/fpdi/fpdi.php');
    $pdfFile =
      Yii::app()->basePath . "/../tags/Pocket_Card arrange_1_hajj_1436.pdf";

    $pdf =
      new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Get Template
    $pageCount = $pdf->setSourceFile($pdfFile);
    $tplIdx = $pdf->importPage(1);

    $pdf->setPrintHeader(false);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetTextColor(0, 0, 0);

    $fonts['Arial'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/arial.ttf', 'TrueTypeUnicode', '', 32);
    $fonts['Faruma'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/faruma.ttf', 'TrueTypeUnicode', '', 32);

    $tagCount = 0;
    foreach ($tags as $tag) {
      if ($tagCount % ($cardColumns * $cardRows) == 0) {
        $pdf->AddPage('L', 'A4');
        $pdf->useTemplate($tplIdx, 0, 0);
      }
      $horizontalOffset =
        $left + (($cardColumns - 1 - ($tagCount % $cardColumns)) * $cardWidth);
      $verticalOffset =
        $top + ((((int)($tagCount / $cardColumns)) % $cardRows) * $cardHeight);

      $this->_generateOneIDBackTag($pdf, $tag, $fonts, $horizontalOffset, $verticalOffset);

      ++$tagCount;
    }

    $pdf->Output();

  }

  private function _generateOneIDBackTag(&$pdf, $tag, $fonts, $xOff, $yOff) {

    // room ++
    $pdf->setXY($xOff + 46, $yOff + 4);
    $pdf->setTextColor(255, 0, 0);
    $pdf->setFont('Arial', 'B', 10);
    $pdf->MultiCell(14.5, 5, $tag->Room, 0, 'C');

    $hotel = null; //Hotels1436::model()->findByPk($tag->Hotel);
    if (!empty($hotel)) {
      $pdf->setTextColor(255, 0, 0);
      // arabic name
      $pdf->setXY($xOff + 3, $yOff + 32.5);
      $pdf->setFont($fonts['Arial'], '', 9.91);
      $pdf->Cell(53.4, 5, $hotel->name_arabic, 0, 1, 'R', 0, '', 1);
      $pdf->setFont($fonts['Arial'], '', 8.91);
      $pdf->setXY($xOff + 3, $yOff + 36);
      $pdf->Cell(53.4, 5, $hotel->address_arabic, 0, 1, 'R', 0, '', 1);
      $pdf->setXY($xOff + 3, $yOff + 39.5);
      $pdf->setFont($fonts['Arial'], '', 8.5);
      $pdf->Cell(53.4, 5, $hotel->name_english, 0, 1, 'R', 0, '', 1);
      $pdf->setXY($xOff + 3, $yOff + 43.5);
      $pdf->setFont($fonts['Arial'], '', 7);
      $pdf->Cell(53.4, 5, $hotel->address_english, 0, 1, 'R', 0, '', 1);
    }

    $color = [255, 255, 255];
    $colors = [
      1 => [0, 155, 0], 2 => [255, 0, 0], 3 => [0, 0, 255]
    ];

    $tlp = TripListPilgrims::model()->findByAttributes([
      'order_no' => $tag->hajji_no,
    ]);

    if (!empty($tlp)) {
      $med = MedicalCheckups::model()->findByAttributes([
        'pilgrim_id' => $tlp->id
      ]);
    }

    if (!empty($med)) {
      $medXOff = 19;
      $medYOff = 3;
      $criticals = ['COPD', 'BA', 'DM', 'HTN', 'Epilepsy'];
      foreach ($criticals as $critical) {
        if ($med->$critical) {
          $pdf->Rect($xOff + $medXOff, $yOff +
            $medYOff, 15, 3.5, 'DF', array(), [
            255, 255, 0
          ]);
          $pdf->setXY($xOff + $medXOff, $yOff + $medYOff);
          $pdf->setFont('Arial', '', 8);
          $pdf->Cell(15, 3.5, ($critical), 0, 1, 'C', 0, '', 1);
          $medYOff += 3.5;
        }
      }
      //      CVarDumper::dump($med->attributes,10,1);die;
    }


    $style2 = array(
      'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0',
      'phase' => 10, 'color' => array(196, 196, 196)
    );

    //    $pdf->Rect($xOff+25.69, $yOff+30.13, 17.66, 22.47, 'DF', array('all' =>
    //      $style2), array(255,255,255));
    //
    ////     image
    //    $imagePath = Yii::app()->params['uploadPath'] . 'umra_member_photos\\' .
    //      str_pad($tag->umra_no,3,'0',STR_PAD_LEFT) . '.png';
    //    $pdf->Image($imagePath, $xOff + 25.89, $yOff + 30.20, 17.40, 0,'', true,
    //      300);
    $pdf->setTextColor(0, 0, 0);

    $pdf->setXY($xOff + 46.80, $yOff + 81.75);
    $pdf->setFont('code39', '', 10);
    $pdf->Cell(20, 8.3, '*' . str_pad($tag->hajji_no, 4, '0', STR_PAD_LEFT) .
      '*', 0, 1, 'R', 0, '', 2);

    // room ++
    $pdf->setXY($xOff + 16, $yOff + 81.5);
    $pdf->setTextColor(50, 50, 50);
    $pdf->setFont('helvetica', 'N', 6);
    $pdf->MultiCell(53, 5, (!empty($hotel) ? $hotel->name_english : "") .
      "\nRoom\nFlt", 0, 'L');

    // room ++ values
    $pdf->setXY($xOff + 20, $yOff + 84.1);
    $pdf->MultiCell(13, 5, $tag->Room .
      //      "\n" . str_pad($tag->Hotel,3,'0',STR_PAD_LEFT) .
      "\n" . str_pad($tag->Flight, 3, '0', STR_PAD_LEFT), 0, 'R');


  }


  public function actionGenerateIDTags() {

    ini_set('memory_limit', '2048M');

    $cardRows = 2;
    $cardColumns = 4;
    $cardWidth = 60;
    $cardHeight = 89.15;
    $top = 14.71;
    $left = 25.0;

    /** @var RamadanUmra1437Fahu15[] $tags */
    $tags = RamadanUmra1437Fahu15::model()->findAll([
      'order' => 'hajji_no asc', //      'limit' => 8,
      //      'offset' => 360
      //      'condition' => 'Flight = "03"',
      //    'condition' => 'Flight IN ("04","06")',
      //      'condition' => 'hajji_no in (917)'
    ]);

    include(Yii::app()->basePath . '/extensions/tcpdf/tcpdf.php');
    include(Yii::app()->basePath . '/extensions/fpdi/fpdi.php');
    $pdfFile =
      Yii::app()->basePath . "/../tags/ramadan_fahu_15_1437_id_card.pdf";

    $pdf =
      new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Get Template
    $pageCount = $pdf->setSourceFile($pdfFile);
    $tplIdx = $pdf->importPage(1);

    $pdf->setPrintHeader(false);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetTextColor(0, 0, 0);

    $fonts['Arial'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/arial.ttf', 'TrueTypeUnicode', '', 32);
    $fonts['Faruma'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/faruma.ttf', 'TrueTypeUnicode', '', 32);

    $tagCount = 0;
    foreach ($tags as $tag) {
      if ($tagCount % ($cardColumns * $cardRows) == 0) {
        $pdf->AddPage('L', 'A4');
        $pdf->useTemplate($tplIdx, 0, 0);
      }
      $horizontalOffset = $left + ($tagCount % $cardColumns * $cardWidth);
      $verticalOffset =
        $top + ((((int)($tagCount / $cardColumns)) % $cardRows) * $cardHeight);

      $this->_generateOneIDTag($pdf, $tag, $fonts, $horizontalOffset, $verticalOffset);

      ++$tagCount;
    }

    $pdf->Output();

  }


  private function _generateOneIDTag(&$pdf, $tag, $fonts, $xOff, $yOff) {


    // room ++
    $pdf->setXY($xOff + 8, $yOff + 88.8);
    $pdf->setTextColor(255, 255, 255);
    $pdf->setFont('helvetica', 'N', 8);
    $pdf->MultiCell(20, 5, 'Room: ' . $tag->Room, 0, 'L');
    //    $pdf->MultiCell(13, 5, "Rm\nBus\nFlt", 0, 'L');

    $pdf->setXY($xOff + 8, $yOff + 78.3);
    $pdf->setTextColor(0, 0, 0);
    $pdf->setFont('helvetica', 'N', 16);
    // room ++ values
    //    $pdf->setXY($xOff + 3, $yOff + 80);
    $pdf->MultiCell(20, 5, 'B' .
      $tag->Bus, //      "\n" . str_pad($tag->Bus,3,'0',STR_PAD_LEFT) .
      //      "\n" . str_pad($tag->Flight,3,'0',STR_PAD_LEFT),
      0, 'L');

    //    $pdf->setTextColor(0, 0, 0);


    //    // name
    //    $pdf->setXY($xOff + 09.50, $yOff + 20.95);
    //    $pdf->setFont('helvetica', '', 14.32);
    //    $pdf->Cell(60, 5, ($tag->full_name_english), 0, 1, 'R', 0, '', 1);

    $pdf->setTextColor(0, 0, 0);


    // arabic name
    $pdf->setXY($xOff + 0, $yOff + 57.2);
    $pdf->setFont($fonts['Arial'], '', 13.91);
    $pdf->Cell(69.96, 5, $tag->full_name_arabic, 0, 1, 'C', 0, '', 1);

    // pp no
    $pdf->setXY($xOff + 0, $yOff + 62.85);
    $pdf->setFont('helvetica', '', 10.47);
    $pdf->Cell(69.96, 5, $tag->pp_no, 0, 1, 'C', 0, '', 1);
    //
    $style2 = array(
      'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0',
      'phase' => 10, 'color' => array(196, 196, 196)
    );

    $hotel = null;//$tag->Hotel;
    $color = [255, 255, 255];
    $colors = [
      1 => [0, 155, 0], 2 => [255, 0, 0], 3 => [0, 0, 255]
    ];

    //    $tlp = TripListPilgrims::model()->findByAttributes([
    //      'order_no' => $tag->hajji_no,
    //    ]);

    //    if (!empty($tlp)) {
    //      $med = MedicalCheckups::model()->findByAttributes([
    //        'pilgrim_id' => $tlp->id
    //      ]);
    //    }

    //    if (!empty($med)) {
    //      $medXOff = 49; $medYOff = 70;
    //      $criticals = ['COPD', 'BA', 'DM', 'HTN', 'Epilepsy'];
    //      foreach ($criticals as $critical) {
    //        if ($med->$critical) {
    //          $pdf->Rect($xOff + $medXOff, $yOff + $medYOff, 15, 3.5, 'DF', array
    //          (), [255,255,0]);
    //          $pdf->setXY($xOff + $medXOff, $yOff + $medYOff);
    //          $pdf->setFont('Arial', '', 8);
    //          $pdf->Cell(15, 3.5, ($critical), 0, 1, 'C', 0, '', 1);
    //          $medYOff += 3.5;
    //        }
    //      }
    ////      CVarDumper::dump($med->attributes,10,1);die;
    //    }
    if (!empty($hotel)) {
      $color = $colors[$hotel];
    }

    //    $pdf->Rect($xOff+3, $yOff+30.13, 15, 10, 'DF', array('all' =>
    //      $style2), $color);

    $pdf->Rect($xOff + 25.69, $yOff + 30.13, 17.66, 22.47, 'DF', array(
      'all' => $style2
    ), array(255, 255, 255));


    // image
    //    $member = Members::model()->findByPk($tag->id);
    if (!empty($tag->passport->photo)) {
      $imagePath =
        'E:\Office Work\Member Management System\Working Source Code\hajjWS\web\files\\' .
        str_pad($tag->passport->photo, 3, '0', STR_PAD_LEFT);
      //    if (file_exists($imagePath)) {

      $pdf->Image($imagePath, $xOff + 25.89, $yOff +
        30.20, 16.90, 0, '', true, 300);
      //    }
    }
    //    $pdf->setXY($xOff + 13.80, $yOff + 80.25);
    //    $pdf->setFont('code39', '', 10);
    //    $pdf->Cell(20, 8.3,'*'.str_pad($tag->hajji_no,4,'0',STR_PAD_LEFT).'*', 0, 1,
    //      'R', 1,'',2);
    //
    $pdf->setTextColor(0, 0, 0);
    // id
    $pdf->setXY($xOff + 0, $yOff + 52);
    $pdf->setFont('helvetica', 'B', 13.7);
    $pdf->Cell(69.96, 5, str_pad($tag->hajji_no, 3, '0', STR_PAD_LEFT), 0, 1, 'C');
    //    $pdf->setTextColor(255, 255, 255);

    //    $pdf->setXY($xOff + 2.90, $yOff + 42.45);
    //    $pdf->setFont($fonts['Arial'], '', 8.21);
    //    $pdf->Cell(10, 5, '۱۴۳۶', 0, 1, 'L');
    //    $pdf->setTextColor(0,0,0);
  }


  public function actionGenerateLuggageTags() {

    ini_set('memory_limit', '2048M');

    $cardRows = 3;
    $cardColumns = 3;
    $cardWidth = 92.607;
    $cardHeight = 61.03;
    $top = 9.38;
    $left = 12.96;

    $tags = RamadanUmra1437Fahu15::model()->findAll([
      'order' => 'hajji_no asc',
      //      'condition' => 'Flight in ("04", "05", "06")',
      //      'limit' => 9,
    ]);

    //    $tag = UmraRamadan1436::model()->findByAttributes(['umra_no' => 284]);

    //    $tags = [$tag, $tag];


    include(Yii::app()->basePath . '/extensions/tcpdf/tcpdf.php');
    include(Yii::app()->basePath . '/extensions/fpdi/fpdi.php');
    $pdfFile =
      Yii::app()->basePath . "/../tags/ramadan_fahu_15_1437_luggage_card.pdf";

    $pdf =
      new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Get Template
    $pageCount = $pdf->setSourceFile($pdfFile);
    $tplIdx = $pdf->importPage(1);

    $pdf->SetTextColor(0, 0, 0);

    $fonts['Arial'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/arial.ttf', 'TrueTypeUnicode', '', 32);
    $fonts['Faruma'] = TCPDF_FONTS::addTTFfont(Yii::app()->basePath .
      '/extensions/tcpdf/fonts/faruma.ttf', 'TrueTypeUnicode', '', 32);

    $tagCount = 0;
    foreach ($tags as $tag) {
      if ($tagCount % ($cardColumns * $cardRows) == 0) {
        $pdf->AddPage('L', 'A4');
        $pdf->useTemplate($tplIdx, 0, 0);
      }
      $horizontalOffset = $left + ($tagCount % $cardColumns * $cardWidth);
      $verticalOffset =
        $top + ((((int)($tagCount / $cardColumns)) % $cardRows) * $cardHeight);

      $this->_generateOneTag($pdf, $tag, $fonts, $horizontalOffset, $verticalOffset);

      ++$tagCount;
    }

    $pdf->Output();

  }

  private function _generateOneTag(&$pdf, $tag, $fonts, $xOff, $yOff) {

    $pdf->setTextColor(0, 0, 0);
    // id
    $pdf->setXY($xOff + 39.90, $yOff + 15);
    $pdf->setFont('helvetica', 'B', 17.7);
    $pdf->Cell(30, 5, str_pad($tag->hajji_no, 3, '0', STR_PAD_LEFT), 0, 1, 'R');

    //    // room ++
    //    $pdf->setXY($xOff + 1, $yOff + 47);
    //    $pdf->setTextColor(50, 50, 50);
    //    $pdf->setFont('helvetica', 'N', 6);
    //    $pdf->MultiCell(13, 5, "Rm\nBus\nFlt", 0, 'L');
    //
    // room ++ values
    $pdf->setXY($xOff + 60, $yOff + 47);
    $pdf->setFont('helvetica', 'N', 14);
    $pdf->MultiCell(30, 5, 'Room: ' . $tag->Room, 0, 'L');

    $pdf->setTextColor(0, 0, 0);


    // name
    $pdf->setXY($xOff + 09.50, $yOff + 20.95);
    $pdf->setFont('helvetica', '', 14.32);
    $pdf->Cell(60, 5, ($tag->full_name_english), 0, 1, 'R', 0, '', 1);


    // arabic name
    $pdf->setXY($xOff + 24.80, $yOff + 26.50);
    $pdf->setFont($fonts['Arial'], '', 16.91);
    $pdf->Cell(45, 5, $tag->full_name_arabic, 0, 1, 'R', 0, '', 1);

    // pp no
    $pdf->setXY($xOff + 38.10, $yOff + 32.65);
    $pdf->setFont('helvetica', '', 12.47);
    $pdf->Cell(12, 5, $tag->pp_no, 0, 1, 'R');

    $hotel = null;// Hotels1436::model()->findByPK($tag->Hotel);

    $colors = [
      1 => [0, 155, 0], 2 => [255, 0, 0], 3 => [0, 0, 255]
    ];
    if ($hotel) {
      // hotel Arabic
      $pdf->setXY($xOff + 54.90, $yOff + 40.45);
      $pdf->setFont($fonts['Arial'], '', 9.59);
      $pdf->Cell(31, 5, $hotel->name_arabic, 0, 1, 'R');

      // Hotel English
      $pdf->setXY($xOff + 54.90, $yOff + 46.15);
      $pdf->setFont('helvetica', '', 7.84);
      $pdf->Cell(31, 5, $hotel->name_english, 0, 1, 'R');

      $color = $colors[$tag->Hotel];
    }

    $styleNone = array(
      'width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0',
      'phase' => 10, 'color' => array(196, 196, 196)
    );

    //    $pdf->Rect($xOff+45, $yOff+45.13, 15, 10, 'DF', array('all' =>
    //      $styleNone), $color);

    $style2 = array(
      'width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0',
      'phase' => 10, 'color' => array(196, 196, 196)
    );

    $pdf->Rect($xOff + 70.49, $yOff + 18.13, 15.66, 19.77, 'DF', array(
      'all' => $style2
    ), array(255, 255, 255));

    // image
    //    $member = Members::model()->findByPk($tag->id);
    if (!empty($tag->passport->photo)) {

      $imagePath =
        'E:\Office Work\Member Management System\Working Source Code\hajjWS\web\files\\' .
        str_pad($tag->passport->photo, 3, '0', STR_PAD_LEFT);
      //    if (file_exists($imagePath)) {

      $pdf->Image($imagePath, $xOff + 70.59, $yOff +
        18.20, 15.40, 0, '', true, 300);
      //    }
    }

    $pdf->setXY($xOff + 20.00, $yOff + 46.25);
    $pdf->setFont('code39', '', 10);
    $pdf->Cell(20, 8.3, '*' . str_pad($tag->hajji_no, 4, '0', STR_PAD_LEFT) .
      '*', 0, 1, 'R', 1, '', 2);
    //
    //    $pdf->setTextColor(255, 255, 255);

    //    $pdf->setXY($xOff + 2.90, $yOff + 42.45);
    //    $pdf->setFont($fonts['Arial'], '', 8.21);
    //    $pdf->Cell(10, 5, '۱۴۳۶', 0, 1, 'L');
    //    $pdf->setTextColor(0,0,0);
  }


  private function _getStatusCodeMessage($status) {
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = [
      200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
      402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found',
      500 => 'Internal Server Error', 501 => 'Not Implemented',
    ];

    return (isset($codes[$status])) ? $codes[$status] : '';
  }

  public function actionExtractPhoneNumbersFromText($text) {
    //    $messageList = MessageList::model()->findByPk($id);
    //    $numbers = Helpers::pickPhoneNumber($messageList->list);
    $numbers = Helpers::pickPhoneNumber($text);
    $phoneNumbers = [];
    Foreach ($numbers as $number) {
      if (strlen($number) >= 7) {
        $phoneNumbers[] = $number;
      }
    }
    $numbersDiv = "<div>" . implode("</div><div>", $phoneNumbers) . "</div>";
    echo $numbersDiv;
  }

  public function actionAddList($listName) {
    //check if list name exists already
    $existingList = MessageList::model()
      ->findByAttributes(['name_english' => trim($listName)]);
    if ($existingList == null) {
      $newList = new MessageList();
      $newList->name_english = trim($listName);
      if ($newList->save()) {
        echo $newList->id;
      } else {
        echo 0;
      }

      return;
    }
    echo -1;
  }

  public function actionDeleteList($id) {
    $deletedRows = MessageList::model()->deleteByPk($id);
    if ($deletedRows > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  public function actionGetList($id) {
    if (empty($id)) {
      return;
    }
    $messageList = MessageList::model()->findByPk((int)$id);
    echo $messageList->number_list;
  }

  public function actionExportCards($fileType = 1, $fromId = null,
    $toId = null) {

    if (empty($fromId)) {
      $fromId = Yii::app()->db->createCommand('SELECT min(mhc_no) FROM members')
        ->queryScalar();
    }

    if (empty($toId)) {
      $toId = Yii::app()->db->createCommand('SELECT max(mhc_no) FROM members')
        ->queryScalar();
    }

    if (isset($_GET['exportNow'])) {
      if (empty($fromId) || empty($toId) || (int)$fromId == 0 ||
        (int)$toId == 0 || (int)$toId < (int)$fromId
      ) {
        Yii::app()->user->setFlash('error', 'Please enter valid values in both From MHC ID and To MHC ID!');
      } else {
        $members =
          Members::model()->findAll('mhc_no >= :fromId AND mhc_no <= :toId', [
            ':fromId' => $fromId, ':toId' => $toId
          ]);
        if (empty($members)) {
          Yii::app()->user->setFlash('error', 'No Member found in specified range');
        } else {
          ini_set('max_execution_time', 300);
          $zip = new ZipArchive();
          $zipfileName =
            Yii::app()->basePath . "/../cards/member_cards_" . $fromId .
            "_to_" . $toId . ".zip";
          if ($zip->open($zipfileName, ZipArchive::OVERWRITE) !== true) {
            Yii::app()->user->setFlash('error', 'An internal error has occured. Please try again' .
              ' or contact the administrator');
          } else {
            $zip->addFile(Yii::app()->basePath .
              "/../images/cardBack.png", 'card_back.jpg');

            foreach ($members as $member) {
              $fileName =
                Yii::app()->basePath . '/../cards/' . $member->person->id_no .
                '.jpg';
              if (!file_exists($fileName)) {
                Helpers::generateCard($member, $display = false);
              }
              $zip->addFile($fileName, 'id_card_' .
                str_pad($member->mhc_no, 7, '0', STR_PAD_LEFT) . '_' .
                $member->person->id_no . '.jpg');
            }
          }
          $zip->close();
          header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: private", false);
          header("Content-Type: application/zip");
          header("Content-Disposition: attachment; filename=" .
            basename($zipfileName) . ";");
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: " . filesize($zipfileName));
          readfile("$zipfileName");
          unlink($zipfileName);
          Yii::app()->end();
          //Yii::app()->user->setFlash('success', sizeof($members) . ' cards generated');
          if ($fileType == 2) { // createpdf
          }
        }
      }
    }

    $this->render('exportCards', [
      'fileType' => $fileType, 'fromId' => $fromId, 'toId' => $toId
    ]);
  }

  //  public function actionUpdatePollData() {
  //    ini_set('max_execution_time', 3000);
  //
  //    // Update Members who are not present in poll data
  //    $memberIdsToUpdate = [];
  //    $membersInPoll = MhcPoll::pollMemberIdList();
  //    if ($membersInPoll == false) {
  //      Yii::app()->user->setFlash('error', 'Could not communicate with the Mobile app server!');
  //      $this->redirect(['site/settings']);
  //    }
  //
  //    $membersInPoll = array_merge(['0'], $membersInPoll);
  //    $memberIdsToUpdate = Yii::app()->db->createCommand("SELECT id FROM members where id NOT IN (" . implode(', ', $membersInPoll) . ")")->queryColumn();
  //    foreach ($memberIdsToUpdate as $memberIdToUpdate) {
  //      MhcPoll::updateMember($memberIdToUpdate);
  //      echo '</br>Member id ' . $memberIdToUpdate . ' pushed!';
  //      ob_flush();
  //      flush();
  //      sleep(3);
  //    }
  //
  //
  //    // Update Members who are not present in poll data
  //    $transactionIdsToUpdate = [];
  //    $transactionsInPoll = MhcPoll::pollTransactionIdList();
  //    if ($transactionsInPoll == false) {
  //      Yii::app()->user->setFlash('error', 'Could not communicate with the Mobile app server!');
  //      $this->redirect(['site/settings']);
  //    }
  //    $transactionsInPoll = array_merge(['0'], $transactionsInPoll);
  //    $transactionIdsToUpdate = Yii::app()->db->createCommand("SELECT transaction_id FROM member_transactions where transaction_id NOT IN (" . implode(', ', $transactionsInPoll) . ")")->queryColumn();
  //    foreach ($transactionIdsToUpdate as $transactionIdToUpdate) {
  //      if (MhcPoll::updateTransaction($transactionIdToUpdate) == false) {
  //        Yii::app()->user->setFlash('error', 'Could not communicate with the Mobile app server!');
  //        $this->redirect(['site/settings']);
  //      }
  //      MhcPoll::updateTransaction($transactionIdToUpdate);
  //      echo '</br>Member Transaction id ' . $transactionIdToUpdate . ' pushed!';
  //      ob_flush();
  //      flush();
  //      sleep(3);
  //    }
  //
  //    Yii::app()->user->setFlash('success', 'Mobile Server Data synchronisation completed!');
  //    $this->redirect(['site/settings']);
  //  }

  public function actionCreateAuditLogsFromOpLog() {
    ini_set('max_execution_time', 300);

    //Application Forms
    $appForms = ApplicationForms::model()->findAll();
    foreach ($appForms as $appForm) {
      $audit =
        new ClientAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_DATA_APPLICATION_FORM, $appForm, [], 'New Application Registration', true);
      $audit->write();
    }
    echo "Application Forms' audits written.<br>";

    //Application Verifications
    $appVerifications = ApplicationFormVerifications::model()->findAll();
    foreach ($appVerifications as $appVerification) {
      $audit =
        new ClientAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_DATA_APPLICATION_VERIFICATION, $appVerification, [], 'New Application Verification', true);
      $audit->write();
    }
    echo "Application Form Verfications' audits written.<br>";

    //Payment Collections
    $payments = MemberTransactions::model()->findAll();
    foreach ($payments as $payment) {
      $audit =
        new ClientAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_DATA_PAYMENT_COLLECTION, $payment, [], 'First Payment Collection', true);
      $audit->write();
    }
    echo "Payment Collections' audits written.<br>";

    //Members
    $members = Members::model()->findAll();
    foreach ($members as $member) {
      $audit =
        new ClientAudit(ClientAudit::AUDIT_ACTION_CREATE, ClientAudit::AUDIT_DATA_MEMBER, $member, [], 'Initial Member Registration (First Payment Collection)', true);
      $audit->addChild(array_filter([
        $member->person, $member->vaaruthaPerson,
        $member->emergencyContactPerson, $member->replacementPerson,
        $member->caretakerPerson
      ]));
      $audit->write();
    }
    echo "Members' audits written.<br>";
  }

  public function actionDataErrors() {

    /*
     * There are three types of system errors.
     * 1. Incomplete member information
     * 2. Wrong data entered into Previous Hajj Year field
     * 3. Wrong transaction times for payment collections
     */

    // Check the number of records with wrong data in Previous Hajj Year Field
    $wrongPreviousHajjYearDataProvider =
      Helpers::wrongPreviousHajjYearDataProvider();
    $wrongPreviousHajjYearCount =
      $wrongPreviousHajjYearDataProvider->totalItemCount;

    // Check the number of records with wrong transaction times due to system error
    $wrongTransactionTimesDataProvider =
      Helpers::wrongTransactionTimesDataProvider();
    $wrongTransactionTimesCount =
      $wrongTransactionTimesDataProvider->totalItemCount;

    // Check if previous Hajj year is entered but not applied for badhal hajj
    $previousHajjYearButNotAppliedForBadhalHajj =
      Helpers::previousHajjYearButNotAppliedForBadhalHajjDataProvider();
    $previousHajjYearButNotAppliedForBadhalHajjCount =
      $previousHajjYearButNotAppliedForBadhalHajj->totalItemCount;

    $this->render('dataErrors', [
      'wrongPreviousHajjYearCount' => $wrongPreviousHajjYearCount,
      'wrongTransactionTimesCount' => $wrongTransactionTimesCount,
      'previousHajjYearButNotAppliedForBadhalHajjCount' => $previousHajjYearButNotAppliedForBadhalHajjCount,
    ]);
  }

  public function actionReverseFixTransactionTimes() {
    $criteria = new CDbCriteria();
    $criteria->condition = 'transaction_id <= 98';
    $criteria->order = 'transaction_time asc';
    $errorTransactions = MemberTransactions::model()->findAll($criteria);
    foreach ($errorTransactions as $errorTransaction) {
      $errorTransaction->transaction_time =
        date('Y-m-d H:i:s', strtotime($errorTransaction->transaction_time) -
          (13 * 60 * 60));
      $dbTransaction = Yii::app()->db->beginTransaction();
      $errorTransaction->save();
      $dbTransaction->commit();
    }
    $this->redirect(['fixTransactionTimes']);
  }

  public function actionFixTransactionTimes() {
    // get all transactions with transaction times before 2nd March 2014
    // these were recorded 14 hours negative to real time due to using time from
    // mysql server of which time was not set to real geographic time
    // these times therefore need to be adjusted

    $criteria = new CDbCriteria();
    $criteria->condition = 'transaction_time < "2014-03-02 09:00:00"';
    $criteria->order = 'transaction_time asc';

    if (!empty($_POST['fixAll'])) {
      $errorTransactions = MemberTransactions::model()->findAll($criteria);
      foreach ($errorTransactions as $errorTransaction) {
        $errorTransaction->transaction_time =
          date('Y-m-d H:i:s', strtotime($errorTransaction->transaction_time) +
            (13 * 60 * 60));
        $dbTransaction = Yii::app()->db->beginTransaction();
        $dbTransaction->doAudit(ClientAudit::AUDIT_ACTION_EDIT, ClientAudit::AUDIT_DATA_PAYMENT_COLLECTION, $errorTransaction, 'Automatic time-error fix by adding 13 hours to incorrect transaction time');
        $errorTransaction->save();
        $dbTransaction->commit();
      }
      Yii::app()->user->setFlash('success', 'Transaction times of all error records have been fixed!');
    }

    $this->render('errorTransactionTimes', [
      'dataProvider' => Helpers::wrongTransactionTimesDataProvider(),
    ]);
  }

  public function actionPreviousHajjYears() {
    $this->render('membersWithPreviousHajj', [
      'dataProvider' => Helpers::wrongPreviousHajjYearDataProvider(),
    ]);
  }

  public function actionNoBadhalHajj() {
    $this->render('membersWithPreviousHajjButNoBadhalHajj', [
      'dataProvider' => Helpers::previousHajjYearButNotAppliedForBadhalHajjDataProvider(),
    ]);
  }

  public function actionMembersWOGenderOrDOBOrPermAddress() {
    $this->redirect(['dataErrors']);
    //    $this->render('membersWOGenderOrDOBOrPermAddress', array(
    //        'dataProvider' => Helpers::incompleteMemberInfoDataProvider(),
    //    ));
  }

  public function actionUngroupedMembers($hajj_list_id) {
    echo CHtml::dropDownList('hajj_list_member_id', '', Helpers::ungroupedMembers($hajj_list_id));
  }

  public function actionMHCMemberNumber($term) {
    if (empty($term) && !is_int($term)) {
      return ['' => ''];
    } else {
      $criteria = new CDbCriteria();
      $criteria->compare('name_english', $term, true);
      echo CJSON::encode(CHtml::listData(ZIslands::model()
        ->findAll($criteria), 'island_id', 'name_english'));
    }
  }

  public function actionMahramDetails($mhc_no = null) {
    $mahram = Members::model()->with('person')->find('mhc_no = ' . $mhc_no .
      ' AND person.gender_id = ' . Constants::GENDER_MALE);
    if ($mahram) {
      echo $mahram->person->personText;
    } else {
      echo 'Please select a mahram';
    }
  }

  public function actionOrganizationBranches($orgId, $model = null,
    $attribute = 'branch_id', $prompt = 'Select Branch', $style = '') {
    $criteria = new CDbCriteria();
    $criteria->order = 'name_english asc';
    $branchList = CHtml::listData(Branches::model()->findAllByAttributes([
      'organization_id' => (int)$orgId,
    ], $criteria), 'id', 'name_english');

    if (!empty($model)) {
      echo CHtml::activeDropDownList(new $model, $attribute, $branchList, [
        'prompt' => $prompt, 'class' => 'form-control', 'style' => $style
      ]);
    } else {
      echo CHtml::dropDownList($attribute, '', $branchList, [
        'prompt' => $prompt, 'class' => 'form-control', 'style' => $style
      ]);
    }
  }

  public function actionAtollIslands($selected_atoll, $model = null,
    $attribute = 'island_id', $prompt = 'Select Island', $style = '') {
    $selected_atoll = (int)$selected_atoll;
    $criteria = new CDbCriteria();
    $criteria->addColumnCondition(['atoll_id' => $selected_atoll,
                                   'is_inhibited' => true]);
    $criteria->order = H::tf('name_dhivehi asc');
    $islandList = CHtml::listData(ZIslands::model()->findAll($criteria), 'island_id', H::tf('name_dhivehi'));

    if (!empty($model)) {
      echo CHtml::activeDropDownList(new $model, $attribute, $islandList, [
        'prompt' => $prompt, 'class' => 'form-control', 'style' => $style
      ]);
    } else {
      echo CHtml::dropDownList($attribute, '', $islandList, [
        'prompt' => $prompt, 'class' => 'form-control', 'style' => $style
      ]);
    }
  }

  // delete uploaded files
  public function actionDeleteUploadedFile() {
    if (!Yii::app()->request->isPostRequest ||
      !Yii::app()->request->isAjaxRequest || Yii::app()->user->isGuest ||
      empty($_POST['fn'])
    ) {
      $status = 0;
    } else {
      $fileName = $_POST['fn'];
      $field = $_POST['f'];
      $id = $_POST['m'];
      if (Helpers::deleteUploadedFile($fileName)) {
        if (!empty($id)) {
          ApplicationFormsHelper::model()->updateByPk($id, [$field => null]);
        }
        $status = 1;
      } else {
        $status = 0;
      }
    }
    echo $status;
  }


  public function actionRunTest() {
    //Helpers::runTest();

    $this->layout = '//layouts/email';

//    Yii::import('ext.PHPMailer.phpmailer');
    include(Yii::app()->basePath . '/extensions/PHPMailer/phpmailer.php');
    $path = realpath(Yii::app()->basePath . "/../" .
      Helpers::config(Constants::IMAGES_PATH_CONFIG_DIRECTIVE)) . '/';
    $mail = new PHPMailer(true);
    try {
      $mail->isHTML(true);
      $mail->SetFrom('admin@mhclonline.com', 'MHCL Administrator');
      $mail->AddAddress('ict@mhcl.mv', 'Mohamed Nazim');
      $mail->Subject = 'Welcome to SDS Portal';

      $mail->addEmbeddedImage($path .
          'back-ground-logo.png', 'bg-image','back-ground-logo.png');
      $mail->addEmbeddedImage($path .
        'sds_welcome_banner.png', 'header-image','sds_welcome_banner.png');

      $mail->Body = $this->render('runTest',null,true);
      $mail->send();

//        mail('ict@mhcl.mv', 'Welcome to SDS Portal',
//          $mail->createBody(),"From: admin@mhcl.mv");

    } catch (phpmailerException $ex) {
      echo $ex->errorMessage();
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }

//    mail('ict@mhcl.mv', 'Welcome to SDS Portal',
//      $mail->createBody(),"From: admin@mhcl.mv");
//
    echo "Mail Sent";

  }
}
