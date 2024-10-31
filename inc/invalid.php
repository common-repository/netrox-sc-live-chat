<?php defined('ABSPATH') or die("Bye!"); ?>

<h1><img class="nx_top_logo" src="<?php echo NETROX_URL_IMG."logo.png"; ?>" alt="<?php _e('Netrox SC Live Chat Settings', 'netroxsc'); ?>" /></h1>
<div class="card">
	<p><?php _e("Please check your mailbox. We have sent you the activation link. Follow it and then click the button below or simply reload this page.", 'netroxsc'); ?></p>

	<form method="post" action="" novalidate="novalidate">
		<input type="hidden" name="action" value="validate" />
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('I followed the link from email message. Check again!', 'netroxsc') ?>"> 
			<input type="submit" name="cancel" class="button" value="<?php _e("Cancel this registration", 'netroxsc') ?>" />
		</p>
	</form>
</div>
