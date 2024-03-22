<?php
/* @var $this RegistrationController */
/* @var $data ApplicationForms */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), ['view', 'id'=>$data->id]); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('state_id')); ?>:</b>
	<?php echo CHtml::encode($data->state_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('application_date')); ?>:</b>
	<?php echo CHtml::encode($data->application_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_no')); ?>:</b>
	<?php echo CHtml::encode($data->id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('applicant_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->applicant_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('applicant_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->applicant_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('applicant_gender_id')); ?>:</b>
	<?php echo CHtml::encode($data->applicant_gender_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('perm_address_island_id')); ?>:</b>
	<?php echo CHtml::encode($data->perm_address_island_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('current_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->current_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('current_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->current_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('d_o_b')); ?>:</b>
	<?php echo CHtml::encode($data->d_o_b); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone_number_1')); ?>:</b>
	<?php echo CHtml::encode($data->phone_number_1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone_number_2')); ?>:</b>
	<?php echo CHtml::encode($data->phone_number_2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email_address')); ?>:</b>
	<?php echo CHtml::encode($data->email_address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('marital_status_id')); ?>:</b>
	<?php echo CHtml::encode($data->marital_status_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('number_of_children')); ?>:</b>
	<?php echo CHtml::encode($data->number_of_children); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('family_contact_no')); ?>:</b>
	<?php echo CHtml::encode($data->family_contact_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_perm_address_island_id')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_perm_address_island_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vaarutha_phone_number')); ?>:</b>
	<?php echo CHtml::encode($data->vaarutha_phone_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_perm_address_island')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_perm_address_island); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_current_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_current_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_current_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_current_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emergency_contact_phone_no')); ?>:</b>
	<?php echo CHtml::encode($data->emergency_contact_phone_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_designation_english')); ?>:</b>
	<?php echo CHtml::encode($data->employed_designation_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_designation_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->employed_designation_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_phone_number_1')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_phone_number_1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_institution_phone_number_2')); ?>:</b>
	<?php echo CHtml::encode($data->employed_institution_phone_number_2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('employed_salary')); ?>:</b>
	<?php echo CHtml::encode($data->employed_salary); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('previous_hajj_year')); ?>:</b>
	<?php echo CHtml::encode($data->previous_hajj_year); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('preferred_year_for_hajj')); ?>:</b>
	<?php echo CHtml::encode($data->preferred_year_for_hajj); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_perm_address_island_id')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_perm_address_island_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_current_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_current_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_current_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_current_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_phone_number_1')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_phone_number_1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_phone_number_2')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_phone_number_2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_relationship_english')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_relationship_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_relationship_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_relationship_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_mhc_membership_no')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_mhc_membership_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('package_preference_id')); ?>:</b>
	<?php echo CHtml::encode($data->package_preference_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deposit_preference_id')); ?>:</b>
	<?php echo CHtml::encode($data->deposit_preference_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_gender_id')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_gender_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_d_o_b')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_d_o_b); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_relationship_english')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_relationship_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_relationship_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_relationship_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_perm_address_island_id')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_perm_address_island_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_current_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_current_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('replacement_current_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->replacement_current_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_perm_address_island_id')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_perm_address_island_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_perm_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_perm_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_perm_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_perm_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_current_address_english')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_current_address_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_current_address_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_current_address_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_phone_number_1')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_phone_number_1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_phone_number_2')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_phone_number_2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_relationship_english')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_relationship_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caretaker_relationship_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->caretaker_relationship_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_1_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->witness_1_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_1_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->witness_1_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_1_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->witness_1_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_2_full_name_english')); ?>:</b>
	<?php echo CHtml::encode($data->witness_2_full_name_english); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_2_full_name_dhivehi')); ?>:</b>
	<?php echo CHtml::encode($data->witness_2_full_name_dhivehi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('witness_2_id_no')); ?>:</b>
	<?php echo CHtml::encode($data->witness_2_id_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_copy')); ?>:</b>
	<?php echo CHtml::encode($data->id_copy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('passport_photo')); ?>:</b>
	<?php echo CHtml::encode($data->passport_photo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mahram_document')); ?>:</b>
	<?php echo CHtml::encode($data->mahram_document); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('medical_document')); ?>:</b>
	<?php echo CHtml::encode($data->medical_document); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('operation_log_id')); ?>:</b>
	<?php echo CHtml::encode($data->operation_log_id); ?>
	<br />

	*/ ?>

</div>