<?php
 use lithium\storage\Session;
 use lithium\g11n\Message;
 use lithium\core\Environment; 
?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title><?php echo MAIN_TITLE;?>: <?php if(isset($title)){echo $title;} ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="<?php if(isset($keywords)){echo $keywords;} ?>">	
	<meta name="description" content="<?php if(isset($description)){echo $description;} ?>">		
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<?php echo $this->html->style(array('/bootstrap/css/datepicker')); ?>	
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<?php if($this->_request->controller=='admin'){ ?>			
	<script src="/bootstrap/js/jquery.js"></script>
	<script src="/bootstrap/js/bootstrap-datepicker.js"></script>	
	<?php }else{
	$this->scripts('<script src="/bootstrap/js/bootstrap-datepicker.js?v='.rand(1,100000000).'"></script>'); 		
	}
	?>
	<?php
	$this->scripts('<script src="/js/main.js?v='.rand(1,100000000).'"></script>'); 	
	$this->scripts('<script src="/bootstrap/js/bootstrap.js"></script>'); 
	?>   		
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 20px;
      }
      /* Custom container */
      .container {
        margin: 0 auto;
      }
      .container > hr {
        margin: 20px 0;
      }
    </style>
</head>
<?php
//	print_r(strlen($_SERVER['REQUEST_URI']));
if(Environment::get('locale')=="en_US"){$locale = "en";}else{$locale = Environment::get('locale');}
?>

<body>
	<?php echo  $this->_render('element', 'header');?> 
	<div id="container" class="container">
		<?php echo $this->content(); ?>
	</div>
	<?php echo  $this->_render('element', 'footer');?>	
<?php echo $this->scripts(); ?>	
<script type="text/javascript">
$(function() {
	$('.tooltip-x').tooltip();
	$("input:text:visible:first").focus();
});
</script>
</body>
</html>