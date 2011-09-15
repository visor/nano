<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo Nano::message()->m('cp-title'); ?>: <?php echo $pageTitle; ?></title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo Assets::style()->import(), (isset($head) ? $head : ''); ?>
</head>
<body>
<div class="container_16">
	<div class="grid_6 prefix_5 suffix_5">
		<h1><a href="/cp" target="_blank" style="color: #FFFFFF"><?php echo Nano::message()->m('cp-title'); ?> - <?php echo $pageTitle; ?></a></h1>
		<div id="login">
			<p class="tip">You just need to hit the button and you're in!</p>
<?php /*
			<p class="error">This is when something is wrong!</p>
*/ ?>
			<form id="form1" name="form1" method="post" action="#">
				<p>
					<label><strong>Username</strong><input type="text" name="textfield" class="inputText" id="textfield" /></label>
				</p>
				<p>
					<label><strong>Password</strong><input type="password" name="textfield2" class="inputText" id="textfield2" /></label>
				</p>
				<a class="black_button" href="/auth"><span>Authentification</span></a>
				<label><input type="checkbox" name="checkbox" id="checkbox" /> Remember me</label>
			</form>
			<br clear="all" />
		</div>
<?php /*
		<div id="forgot">
			<a href="#" class="forgotlink"><span>Forgot your username or password?</span></a></div>
		</div>
*/ ?>
	</div>
	<br clear="all" />
</body>
</html>