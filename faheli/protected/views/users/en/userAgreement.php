<?php
/* @var $this UsersController */
/* @var $form CActiveForm */

?>

<h3>Welcome to MHCL Faheli Portal</h3>


<?php
$form = $this->beginWidget('CActiveForm', array(
  'id' => 'user-agreement-form',
  // Please note: When you enable ajax validation, make sure the corresponding
  // controller action is handling ajax validation correctly.
  // There is a call to performAjaxValidation() commented in generated controller code.
  // See class documentation of CActiveForm for details on this.
  'enableAjaxValidation' => false,
));
$person = new Persons();
$person->agreed_to_terms_of_use = 1;
?>

<div class="panel panel-success">
  <div class="panel-heading">Terms of Use of Faheli Portal</div>
  <div class="panel-body" style="padding: 50px;letter-spacing: 1px">
    <p>Welcome to Maldives Hajj Corporation Faheli Portal. Before using this
      portal please read these Terms of Use and agree to it.
    </p>
    <h3>Usage of this portal</h3>
    <ul>
      <li>All information availed via this portal are properties of
        Maldives Hajj Corporation Limited (MHCL).</li>
      <li>Any information on this site other than your personal information may
        not be shared with a 3rd party, unless prior written permission
        from MHCL</li>
      <li>MHCL shall not be held responsible for sharing or misuse of
        the Phone number and Sign-in code used to login to this portal.
      </li>
      <li>All communication exchanged between MHCL and you via this portal
        are logged as per audit regulations, and as such, any communication
        made via this portal must be made under this understanding and MHCL
        emphasises you to always use up to date and correct information.
      </li>
    </ul>
      <h3>Payments</h3>
    <ul>
      <li>All payments made via this portal will be made in Maldivian
        Rufiyaa. Even if you use a bank card of another currency, MHCL will
        receive these in bank exchanged Maldivian Rufiyaa equivalent.
      </li>
      <li>Refunds on payments made via this portal, shall be refunded as
        per applicable and current refund policies after deducting any
        administrative or related fees.</li>
      <li>A valid debit card is required to make payments via this portal.
        MHCL reserves the right not to favor any requested transactions
        without restriction. You bear the responsibility to ensure that
        correct payment information is provided, and the responsibility to
        ensure that you have legal rights to use the payment medium
        prescribed.</li>
      <li>Payments will be processed using Secure Socket Layer over the
        internet by connecting to the opted Bank's Payment Gateway. No
        information from the bank card used, will be available to MHCL or
        this portal.</li>
      <li>All information relating to payments made via this portal, or other
        mediums, or payments made directly at MHCL offices are confidential.
        None of these will be shared by MHCL to another party unless legally
        required.</li>
      <li>All details of payment records should be kept up to date by the
        Payee. As such, MHCL advices that you keep all records of payments
        made via this portal.</li>
    </ul>
    <p></p>
    <p><strong>Please read the above terms carefully, and declare
        your agreement by pressing the button marked
        "I agree".
        You may not use this portal if you do not agree to these terms.
      </strong></p>
    <p>Thank you</p>
    <p>Maldives Hajj Corporation Limiited</p>

  </div>
  <div class="panel-heading">
    <div class="row">
    <div class="col-md-2 col-md-offset-5 text-center">
      <?=$form->hiddenField($person, 'agreed_to_terms_of_use')?>
      <button class="btn btn-sm btn-primary"><H4>I agree</H4></button>
    </div>
    </div>
  </div>
</div>
<?php
$this->endWidget();
