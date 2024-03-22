<?php /* @var $this Controller */
$member = empty(Yii::app()->user->member)?null:Yii::app()->user->member;
$umraPilgrims = empty(Yii::app()->user->umraPilgrims)?null: Yii::app()->user->umraPilgrims;
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="language" content="en"/>
  <link rel="shortcut icon"
        href="<?=Yii::app()->request->baseUrl;?>/images/favicon.ico"
        type="image/x-icon">
  <link rel="icon"
        href="<?=Yii::app()->request->baseUrl;?>/images/favicon.ico"
        type="image/x-icon">
  <link rel="stylesheet" type="text/css"
        href="<?=Yii::app()->request->baseUrl;?>/css/print.css"
        media="print"/>
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css"
        href="<?php // echo Yii::app()->request->baseUrl; ?>/css/ie.css"
        media="screen, projection"/>
  <![endif]-->
  <!--[if lt IE 9]>
  <script
    src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
  <![endif]-->


  <link rel="stylesheet"
        href="<?=Yii::app()->request->baseUrl;?>/fa/font-awesome.min.css"/>
  <link rel="stylesheet"
        href="<?=Yii::app()->request->baseUrl;?>/css/bootstrap.min.css"/>
  <link rel="stylesheet" type="text/css"
        href="<?=Yii::app()->request->baseUrl;?>/css/main.css"
        media="screen"/>
  <?php if(Yii::app()->session['lang']=='dv') {?>
    <link rel="stylesheet"
        href="<?=Yii::app()->request->baseUrl;?>/css/bootstrap-rtl.min.css"/>
  <!-- Custom styles for this template -->
  <link href="<?=Yii::app()->request->baseUrl;?>/css/rtl.css" rel="stylesheet">
  <?php } ?>
  <title>MHCL Online Portal</title>
</head>

<?php $animateStyle = 'style = "display:none"';?>

<body role="document" <?=!empty($_GET['animate'])?$animateStyle:""?>>

<header>
  <!-- Navbar fixed top -->
  <!-- Load Bootstrap RTL theme from CDNJS -->
  <div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse"
                data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
          <a class="navbar-brand" href="<?=Yii::app()->createUrl('/')?>"
             style="margin-top: -5px">
            <span id="logo">
            <?php echo CHtml::image(Helpers::sysUrl(Constants::IMAGES) .
              'logo-only-small.png', Yii::app()->name, [
              'style' =>'margin-top:-5px;max-width:40px'
            ]); ?>
            </span> <?=H::t('site','MHCL')?></a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav ">
          <?php if (!Yii::app()->user->isGuest) { ?>
            <li class="<?=Yii::app()->request->pathInfo == 'members/statement'
              ? 'active' : ''?>"><a
                href="<?=Yii::app()->createUrl('members/statement')?>"><?=H::t('site', 'hajjAccount')?></a></li>
            <li class="<?=Yii::app()->request->pathInfo == 'umra/available'
              ? 'active' : ''?>"><a
                href="<?=Yii::app()->createUrl('umra/available')?>"><?=H::t('site', 'umra')?></a>
            </li>
            <li class="<?=Yii::app()->request->pathInfo == 'ageega/list'
              ? 'active' : ''?>"><a
                href="<?=Yii::app()->createUrl('ageega/list')?>">
                <?=H::t('site', 'ageega')?></a></li>
            <li><a
                href="<?=Yii::app()->createUrl('site/logout')?>"><?=H::t('site', 'logout')?></a>
            </li>
          <?php } ?>
        </ul>
        <ul class="nav navbar-nav pull-left">
          <li><a
              href="<?=Yii::app()->createUrl('site/lang')
              ?>"><?=H::t('site', 'language')?></a>
          </li>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>

</header>
<section id="body" class="container"><?php echo $content; ?></section>
<footer class="container">
  <hr style="margin-bottom: 5px;">
  <div class="small" style="text-align: center; color: grey">
    <?=H::t('site','mhcl_long')?><br/>&copy;
    <?=date('Y')?>
    <br/></div>
</footer><!-- footer -->
<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<script
  src="<?=Yii::app()->request->baseUrl;?>/js/bootstrap.min.js"></script>
<script type="text/javascript"
        src="<?=Yii::app()->request->baseUrl;?>/js/helpers.js"></script>


<?php
  if (!empty($_GET['animate'])) {
?>
  <script>
    $('body').slideToggle();
  </script>
<?php } ?>
<?php if (Yii::app()->language <> 'dv') { ?>
  <script>
    $('.pull-left').addClass('pull-left-temp').removeClass('pull-left');
    $('.pull-right').addClass('pull-left').removeClass('pull-right');
    $('.pull-left-temp').addClass('pull-right').removeClass('pull-left-temp');
  </script>
<?php } ?>
</body>
</html>
