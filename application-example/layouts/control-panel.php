<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $application->message->m('cp-title'); ?>: <?php echo $pageTitle; ?></title>
<?php /* <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> */ ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php /* TODO: use assets module echo Assets::style()->import(), (isset($head) ? $head : ''); */ ?>
	<script type="text/javascript">/*<![CDATA[*/ var email = function(s) { var r = function ( t, u, v ) { return String.fromCharCode(((t - u + v ) % (v * 2)) + u); }, b = [], c, i = s.length, a = "a".charCodeAt(), z = a + 26, A = "A".charCodeAt(), Z = A + 26; while (i--) { c = s.charCodeAt(i); if (c >= a && c < z) { b[i] = r(c, a, 13); } else if (c >= A && c < Z) { b[i] = r(c, A, 13); } else { b[i] = s.charAt(i); } }; return b.join(""); }; /*]]>*/</script>
</head>
<body>
	<div class="container_16" id="wrapper">
		<div class="grid_8" id="logo"><a href="/cp" style="color: #fff"><?php echo $application->message->m('cp-title'); ?></a></div>
		<div class="grid_8">
			<div id="user_tools"><span>Welcome <a href="#">Admin Username</a> | <a href="/logout">Logout</a></span></div>
		</div>
		<div class="grid_16" id="header">
			<div id="menu">
				<ul class="group" id="menu_group_main">
					<li class="item first" id="one"><a href="/cp" class="main current"><span class="outer"><span class="inner dashboard">Dashboard</span></span></a></li>
					<li class="item middle" id="two"><a href="/cp/content" class="main"><span class="outer"><span class="inner content">Content</span></span></a></li>
					<li class="item middle" id="five"><a href="/cp/media" class="main"><span class="outer"><span class="inner media_library">Media Library</span></span></a></li>
<?php /*
					<li class="item middle" id="three"><a href="#"><span class="outer"><span class="inner reports png">Reports</span></span></a></li>
					<li class="item middle" id="four"><a href="#" class="main"><span class="outer"><span class="inner users">Users</span></span></a></li>
					<li class="item middle" id="six"><a href="#" class="main"><span class="outer"><span class="inner event_manager">Event Manager</span></span></a></li>
					<li class="item middle" id="seven"><a href="#" class="main"><span class="outer"><span class="inner newsletter">Newsletter</span></span></a></li>
*/?>
					<li class="item last" id="eight"><a href="/cp/settings" class="main"><span class="outer"><span class="inner settings">Settings</span></span></a></li>
				</ul>
			</div>
		</div>
		<?php if (empty($noTabs)): ?><div class="grid_16"><div id="tabs"><div class="container"><ul><?php include $renderer->getViewFileName(isSet($tabs) ? $tabs : $controller, '_tabs'); ?></ul></div></div></div><?php endif; ?>
		<div class="grid_16" id="content">
			<div class="grid_9"><h1 class="<?php echo $pageClass; ?>"><?php echo $pageTitle; ?></h1></div>
			<div class="clear"></div>
			<?php echo $helper->ui()->showMessages('<div class="grid_15 textcontent">', '</div>'); ?>
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="container_16" id="footer">
		<div class="grid_8" style="text-align: left;">&copy; studio-v</div>
		<div class="grid_8" style="text-align: right;">Website Administration Share by <a href="http://nicetheme.net/">Nice Theme</a></div>
	</div>
	<?php /* TODO: use assets module echo Assets::script()->import(), $helper->scripts()->captured(); */ ?>
</body>
</html>
