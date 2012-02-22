<?php
/**
 * @var Nano_HelperBroker $helper
 * @var string $name1
 * @var string $name2
 */
?>
<html>
<head>
	<title>View cookies</title>
</head>
<body>

<pre id="cookie-array"><?php echo var_export($_COOKIE, true); ?></pre>
<pre id="values">
name1 = [<?php echo $name1; ?>];
name2 = [<?php echo $name2; ?>];
</pre>
<script type="text/javascript">
	document.write('<pre id="cookie-value">[' + document.cookie + ']</pre>');
</script>

<a id="gotoView" href="/cookie/view">gotoView</a>
<br /><a id="gotoSet" href="/cookie/set">gotoSet</a>
<br /><a id="gotoErase" href="/cookie/erase">gotoErase</a>
<br />
<br /><a id="gotoSetHttp" href="/cookie/set?http=1">gotoSet</a>
<br /><a id="gotoEraseHttp" href="/cookie/erase?http=1">gotoEraseHttp</a>

</body>
</html>