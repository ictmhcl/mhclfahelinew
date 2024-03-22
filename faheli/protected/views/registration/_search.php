<?php
/* @var $this RegistrationController */
/* @var $model ApplicationForms */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', [
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
]); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'state_id'); ?>
		<?php echo $form->textField($model,'state_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'application_date'); ?>
		<?php echo $form->textField($model,'application_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'id_no'); ?>
		<?php echo $form->textField($model,'id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'applicant_full_name_english'); ?>
		<?php echo $form->textField($model,'applicant_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'applicant_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'applicant_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'applicant_gender_id'); ?>
		<?php echo $form->textField($model,'applicant_gender_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'perm_address_island_id'); ?>
		<?php echo $form->textField($model,'perm_address_island_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'perm_address_english'); ?>
		<?php echo $form->textField($model,'perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'current_address_english'); ?>
		<?php echo $form->textField($model,'current_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'current_address_dhivehi'); ?>
		<?php echo $form->textField($model,'current_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'d_o_b'); ?>
		<?php echo $form->textField($model,'d_o_b'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'phone_number_1'); ?>
		<?php echo $form->textField($model,'phone_number_1', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'phone_number_2'); ?>
		<?php echo $form->textField($model,'phone_number_2', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'email_address'); ?>
		<?php echo $form->textField($model,'email_address', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'marital_status_id'); ?>
		<?php echo $form->textField($model,'marital_status_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'number_of_children'); ?>
		<?php echo $form->textField($model,'number_of_children'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'family_contact_no'); ?>
		<?php echo $form->textField($model,'family_contact_no', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_full_name_english'); ?>
		<?php echo $form->textField($model,'vaarutha_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'vaarutha_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_id_no'); ?>
		<?php echo $form->textField($model,'vaarutha_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_perm_address_island_id'); ?>
		<?php echo $form->textField($model,'vaarutha_perm_address_island_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_perm_address_english'); ?>
		<?php echo $form->textField($model,'vaarutha_perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'vaarutha_perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'vaarutha_phone_number'); ?>
		<?php echo $form->textField($model,'vaarutha_phone_number', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_full_name_english'); ?>
		<?php echo $form->textField($model,'emergency_contact_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'emergency_contact_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_id_no'); ?>
		<?php echo $form->textField($model,'emergency_contact_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_perm_address_island'); ?>
		<?php echo $form->textField($model,'emergency_contact_perm_address_island'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_perm_address_english'); ?>
		<?php echo $form->textField($model,'emergency_contact_perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'emergency_contact_perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_current_address_english'); ?>
		<?php echo $form->textField($model,'emergency_contact_current_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_current_address_dhivehi'); ?>
		<?php echo $form->textField($model,'emergency_contact_current_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emergency_contact_phone_no'); ?>
		<?php echo $form->textField($model,'emergency_contact_phone_no', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_name_english'); ?>
		<?php echo $form->textField($model,'employed_institution_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_name_dhivehi'); ?>
		<?php echo $form->textField($model,'employed_institution_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_address_english'); ?>
		<?php echo $form->textField($model,'employed_institution_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_address_dhivehi'); ?>
		<?php echo $form->textField($model,'employed_institution_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_designation_english'); ?>
		<?php echo $form->textField($model,'employed_designation_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_designation_dhivehi'); ?>
		<?php echo $form->textField($model,'employed_designation_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_phone_number_1'); ?>
		<?php echo $form->textField($model,'employed_institution_phone_number_1', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_institution_phone_number_2'); ?>
		<?php echo $form->textField($model,'employed_institution_phone_number_2', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'employed_salary'); ?>
		<?php echo $form->textField($model,'employed_salary'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'previous_hajj_year'); ?>
		<?php echo $form->textField($model,'previous_hajj_year', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'preferred_year_for_hajj'); ?>
		<?php echo $form->textField($model,'preferred_year_for_hajj'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_full_name_english'); ?>
		<?php echo $form->textField($model,'mahram_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'mahram_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_id_no'); ?>
		<?php echo $form->textField($model,'mahram_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_perm_address_island_id'); ?>
		<?php echo $form->textField($model,'mahram_perm_address_island_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_perm_address_english'); ?>
		<?php echo $form->textField($model,'mahram_perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'mahram_perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_current_address_english'); ?>
		<?php echo $form->textField($model,'mahram_current_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_current_address_dhivehi'); ?>
		<?php echo $form->textField($model,'mahram_current_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_phone_number_1'); ?>
		<?php echo $form->textField($model,'mahram_phone_number_1', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_phone_number_2'); ?>
		<?php echo $form->textField($model,'mahram_phone_number_2', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_relationship_english'); ?>
		<?php echo $form->textField($model,'mahram_relationship_english', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_relationship_dhivehi'); ?>
		<?php echo $form->textField($model,'mahram_relationship_dhivehi', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_mhc_membership_no'); ?>
		<?php echo $form->textField($model,'mahram_mhc_membership_no', ['size'=>20,'maxlength'=>20]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'package_preference_id'); ?>
		<?php echo $form->textField($model,'package_preference_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deposit_preference_id'); ?>
		<?php echo $form->textField($model,'deposit_preference_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_full_name_english'); ?>
		<?php echo $form->textField($model,'replacement_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'replacement_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_id_no'); ?>
		<?php echo $form->textField($model,'replacement_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_gender_id'); ?>
		<?php echo $form->textField($model,'replacement_gender_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_d_o_b'); ?>
		<?php echo $form->textField($model,'replacement_d_o_b'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_relationship_english'); ?>
		<?php echo $form->textField($model,'replacement_relationship_english', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_relationship_dhivehi'); ?>
		<?php echo $form->textField($model,'replacement_relationship_dhivehi', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_perm_address_island_id'); ?>
		<?php echo $form->textField($model,'replacement_perm_address_island_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_perm_address_english'); ?>
		<?php echo $form->textField($model,'replacement_perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'replacement_perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_current_address_english'); ?>
		<?php echo $form->textField($model,'replacement_current_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'replacement_current_address_dhivehi'); ?>
		<?php echo $form->textField($model,'replacement_current_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_full_name_english'); ?>
		<?php echo $form->textField($model,'caretaker_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'caretaker_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_id_no'); ?>
		<?php echo $form->textField($model,'caretaker_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_perm_address_island_id'); ?>
		<?php echo $form->textField($model,'caretaker_perm_address_island_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_perm_address_english'); ?>
		<?php echo $form->textField($model,'caretaker_perm_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_perm_address_dhivehi'); ?>
		<?php echo $form->textField($model,'caretaker_perm_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_current_address_english'); ?>
		<?php echo $form->textField($model,'caretaker_current_address_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_current_address_dhivehi'); ?>
		<?php echo $form->textField($model,'caretaker_current_address_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_phone_number_1'); ?>
		<?php echo $form->textField($model,'caretaker_phone_number_1', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_phone_number_2'); ?>
		<?php echo $form->textField($model,'caretaker_phone_number_2', ['size'=>25,'maxlength'=>25]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_relationship_english'); ?>
		<?php echo $form->textField($model,'caretaker_relationship_english', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caretaker_relationship_dhivehi'); ?>
		<?php echo $form->textField($model,'caretaker_relationship_dhivehi', ['size'=>60,'maxlength'=>100]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_1_full_name_english'); ?>
		<?php echo $form->textField($model,'witness_1_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_1_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'witness_1_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_1_id_no'); ?>
		<?php echo $form->textField($model,'witness_1_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_2_full_name_english'); ?>
		<?php echo $form->textField($model,'witness_2_full_name_english', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_2_full_name_dhivehi'); ?>
		<?php echo $form->textField($model,'witness_2_full_name_dhivehi', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'witness_2_id_no'); ?>
		<?php echo $form->textField($model,'witness_2_id_no', ['size'=>7,'maxlength'=>7]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'id_copy'); ?>
		<?php echo $form->textField($model,'id_copy', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'passport_photo'); ?>
		<?php echo $form->textField($model,'passport_photo', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mahram_document'); ?>
		<?php echo $form->textField($model,'mahram_document', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'medical_document'); ?>
		<?php echo $form->textField($model,'medical_document', ['size'=>60,'maxlength'=>255]); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'operation_log_id'); ?>
		<?php echo $form->textField($model,'operation_log_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->