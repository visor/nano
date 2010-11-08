<?php foreach ($variables as $name => $value): ?>
<div style="margin: .5em;">
<label for="variable-<?php echo $name; ?>" style="font-size: 1.5em; font-weight: bold;"><code>$<?php echo $name; ?></code></label>
<pre id="variable-<?php echo $name; ?>" style="width: 50%; max-height: 200px; overflow: auto;">
<?php echo null === $value || is_scalar($value) ? var_export($value, true) : htmlSpecialChars(print_r($value, true)); ?>
</pre>
</div>
<?php endforeach; ?>