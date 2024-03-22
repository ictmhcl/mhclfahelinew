<?php
return [
  'plannedTrips' => 'Forthcoming Umra Trips',
  'registerFor {umra}' => 'Register for umra',
  'chooseMahram' => 'Select a Mahram',

  // Form labels
  'mahramId' => 'Mahram ID No',
  'mahramInfo' => 'Females below 45 years of age should also register their
  Mahram to this Umra. Select the ID of Mahram after registering Mahram.
  Please attach Government issued translation of Mahram Proof Document below.',
  'mahramIdError' => 'Mahram ID is incorrect. Please register Mahram first.',
  'mahramMaleRequired' => 'Please choose a male member as a Mahram',
  'currentAddress' => 'Current Address',
  'current_island_id' => 'Island',
  'current_address_english' => 'Address (English)',
  'current_address_dhivehi' => 'Address (Thaana)',
  'groupInfo' => 'Group registration',
  'application_form' => 'Umra Application Form',
  'downloadFormInfo' => 'By clicking the submit button below, I hereby agree to and accept the '
    . CHtml::link('terms and conditions', Constants::REGISTRATION_PDF, [
      'target' => '_blank', 'id' => 'applicationFormDownloadLink',
      'style' => 'text-decoration: underline'
    ]) . '.',

  // Umra Details
  'registeredInfo {fullName} {id_no} {tripName}' => 'fullName (id_no) has
  been registered for tripName',
  'umraPrice' => 'Umra Price',
  'pastDeadline' => 'Past Deadline',
  'dates' => 'Dates', 'departureDate' => 'Departure Date',
  'arrivalDate' => 'Return Date', 'deadlineDate' => 'Deadline Date',
  'discounts' => 'Discounts',
  'discountInfo' => 'Please contact us if you would like to claim for a
  discount. For group discounts, all members in the group must be already
  registered.',

  'emergencyContact' => 'Emergency Contact',
  'ec_island_id' => 'Island',
  'ec_address_english' => 'Address (English)',
  'ec_address_dhivehi' => 'Address (Thaana)',
  'ec_full_name' => 'Name',
  'ec_phone_number' => 'Phone Number',
  

];
