<?php
/* @var $this MembersController */
/* @var $model Members */
/* @var $form CActiveForm */
?>

<div class="form wide wide2">

    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'members-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ]);
    ?>
    <fieldset><legend><h3>Summary</h3></legend>
        <div clacc="row" style="text-align: right;">
            <?php echo '<span class="statusLabel">Registration Status:</span> <span class="statusText">' . $model->state->name_english . '</span>' ?>
        </div>
        <hr style="background-color: #bbb; height: 1px; margin-top: 8px">
        <table>
            <tr>
                <td width="560px">
                    <div class="row">
                        <?php echo Chtml::label('Applicant', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            $model->person->getPersonText(),
                            $model->phone_number_1,
                            $model->email_address
                        ]));
                        ?>
                    </div>
                    <div class="row">
                        <?php echo Chtml::label('Family', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            $model->maritalStatus->name_english,
                            $model->number_of_children . ' children',
                            $model->vaaruthaPerson->getPersonText(),
                            $model->vaarutha_phone_number,
                            $model->family_contact_no
                        ]));
                        ?>
                    </div>
                    <div class="row">
                        <?php echo Chtml::label('Emergency Contact', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            $model->emergencyContactPerson->getPersonText(),
                            $model->emergency_contact_phone_no
                        ]));
                        ?>
                    </div>
                    <div class="row">
                        <?php if (!empty($model->employed_institution_name_english)) { ?>
                            <?php echo Chtml::label('Job', ''); ?>
                            <?php
                            echo implode(', ', array_filter([
                                $model->employed_designation_english,
                                $model->employed_institution_name_english,
                                $model->employed_institution_address_english,
                                $model->employed_institution_phone_number_1,
                                'MVR ' . number_format($model->employed_salary, 0, '.', ',')
                            ]));
                            ?>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <?php echo Chtml::label('Hajj Info', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            'Previous Hajjs: ' . (empty($model->previous_hajj_year) ? 'N/A' : $model->previous_hajj_year),
                            'Preferred: ' . (empty($model->preferred_year_for_hajj) ? 'N/A' : $model->preferred_year_for_hajj),
                        ]));
                        ?>
                    </div>
                    <?php if (!empty($model->mhc_membership_np)) { ?>
                        <div class="row">
                            <?php echo Chtml::label('Mahram'); ?>
                            <?php
                            echo implode(', ', array_filter([
                                $model->mahramMember->person->getPersonText(),
                                $model->mahram_phone_no,
                                $model->mahram_relationship_english
                            ]));
                            ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <?php echo Chtml::label('Deposits', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            $model->packagePreference->name_english,
                            $model->depositPreference->name_english
                        ]));
                        ?>
                    </div>
                    <?php if (!empty($model->replacement_person_id)) { ?>
                        <div class="row">
                            <?php echo Chtml::label('Replacement', ''); ?>
                            <?php
                            echo implode(', ', array_filter([
                                $model->replacementPerson->getPersonText(),
                                $model->replacement_relationship_english
                            ]));
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($model->caretaker_person_id)) { ?>
                        <div class="row">
                            <?php echo Chtml::label('Caretaker', ''); ?>
                            <?php
                            echo implode(', ', array_filter([
                                $model->caretakerPerson->getPersonText(),
                                $model->caretaker_phone_number_1,
                                $model->caretaker_relationship_english,
                            ]));
                            ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <?php echo Chtml::label('Witnesses', ''); ?>
                        <?php
                        echo implode(', ', array_filter([
                            '1: ' . $model->applicationForm->witness_1_full_name_english,
                            $model->applicationForm->witness_1_id_no,
                            '2: ' . $model->applicationForm->witness_1_full_name_english,
                            $model->applicationForm->witness_2_id_no
                        ]));
                        ?>
                    </div>
                </td>
                <td style="text-align: center">
                    <p></p>
                    <div class="row">
                        <?php
                        echo CHtml::image(Helpers::sysUrl(Constants::UPLOADS) . $model->applicationForm->passport_photo, $model->person->full_name_english, [
                            'style' => 'max-height:120px;max-width:120px;'
                        ]);
                        ?><br><br>
                    </div>
                    <div style="text-align: left;">
                        <span class="statusText">1435 Hajj Registration</span>
                    </div>

                    <div class="row">
                        <?php echo CHtml::label('Payment Rcvd.', 'feeCollected', ['style'=>'width:100px']); ?>
                        <?php echo CHtml::checkBox("feeCollected", false) ; ?>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Amount:', 'amount', ['style'=>'width:100px']); ?>
                        <?php echo CHtml::textField("amount",Helpers::config('registrationFee'), ['style'=>'width:70px;font-size:14px;font-weight:bold;background:lightblue;text-align:right']) ?>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Payment mode:', 'transactionMode', ['style'=>'width:100px']); ?>
                        <?php echo CHtml::dropDownList("transactionMode",'',
                                CHtml::listData(ZTransactionMediums::model()->findAll(),'id', 'name_english')) ?>
                    </div>
                    <div class="buttons">
                        <?php echo CHtml::submitButton('Print Receipt & Register Member', [
                            'style'=>'background-color: green;color: white;',
                            'onClick'=>'
                                if($("#feeCollected").is(":checked")==false){
                                    alert("You must confirm that you have collected the payment by marking the check box labelled \'Payment Rcvd.\'");
                                    turnBlockOn = false;
                                    return false;
                                } else {
                                    if (confirm("Are you sure that the values you are submitting are CORRECT? This cannot be reversed!")==false) {
                                        turnBlockOn = false;
                                        return false;
                                    }
                                }'
                        ]); ?>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <?php $this->endWidget(); ?>

</div><!-- form -->
