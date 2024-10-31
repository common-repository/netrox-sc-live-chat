<?php defined('ABSPATH') or die("Bye!"); ?>

<script type="text/javascript">
	nxSignupSwitch = function(signup) {
		var login_container = document.getElementById('nx_login_container'),
			signup_container = document.getElementById('nx_signup_container');
		if (signup) {
			login_container.style.display = "none";
			signup_container.style.display = "block";
		} else {
			signup_container.style.display = "none";
			login_container.style.display = "block";
		}
	};
</script>
<h1><img class="nx_top_logo" src="<?php echo NETROX_URL_IMG."logo.png"; ?>" alt="<?php _e('Netrox SC Live Chat Settings', 'netroxsc'); ?>" /></h1>
<?php	if (isset($this->errors["AUTH"])): ?>
<div class="error">
	<p><?php _e("Sorry, couldn't authenticate the user. Please login.", 'netroxsc'); ?></p>
</div>
<?php 	elseif (isset($this->errors["CONNECT"])): ?>
<div class="error">
	<p><?php _e("Error occured while trying to connect to Netrox SC servers. Please try again later.", 'netroxsc'); ?></p>
</div>
<?php 	endif; ?>
<div id="nx_login_container" style="<?php if ($this->showSignup) echo "display:none;"; ?>">
	<div class="card">
		<p><?php _e('Login as a Netrox SC user to bind your live chat account to this site.', 'netroxsc'); ?></p>
		<p><?php _e('If you don\'t have an account yet please click the corresponding button at the bottom of the form.', 'netroxsc'); ?></p>
		<p><?php echo str_replace('##URL##', NETROX_URL_WEB, __('If you face any difficulties please ask our support team for help in live chat at <a href="##URL##">##URL##</a>', 'netroxsc')); ?></p>
		
		<form method="post" action="" novalidate="novalidate">
			<input type="hidden" name="action" value="login" />
			
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="nx_inp_email"><?php _e('Email'); ?></label>
						</th>
						<td>
							<input name="login" type="text" id="nx_inp_email" value="" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_inp_pass"><?php _e('Password'); ?></label>
						</th>
						<td>
							<input name="password" type="password" id="nx_inp_pass" class="regular-text" />
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Log In','netroxsc') ?>"> 
				<button type="button" onclick="nxSignupSwitch(true)" id="nx_switch_signup" class="button"><?php _e("I don't have a Netrox SC account yet", 'netroxsc') ?></button>
			</p>
			<p>
				<a href="<?php echo(NETROX_URL_SYS."password_recover"); ?>" target="_blank"><span><?php _e('I forgot my password','netroxsc') ?></span></a>
			</p>
		</form>
	</div>
</div>
<div id="nx_signup_container" style="<?php if (!$this->showSignup) echo "display:none;" ?>">
	<div class="card">
		<p><?php _e('Fill in the following form to start using Netrox.', 'netroxsc'); ?></p>
		<p><?php echo str_replace('##URL##', NETROX_URL_WEB, __('If you face any difficulties please ask our support team for help in live chat at <a href="##URL##">##URL##</a>','netroxsc')); ?></p>
		<form method="post" action="" novalidate="novalidate">
			<input type="hidden" name="action" value="signup" />
			
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_email"><?php _e('Email', 'netroxsc'); ?></label>
						</th>
						<td>
							<input name="email" type="email" aria-describedby="nx_signup_descr_email" id="nx_signup_inp_email" value="<?php echo($sLogin); ?>" class="regular-text" />
							<p class="description" id="nx_signup_descr_email"><?php _e('Login for future Netrox SC account administrator', 'netroxsc'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_website"><?php _e('Base domain', 'netroxsc'); ?></label>
						</th>
						<td>
							<input name="website" type="text" aria-describedby="nx_signup_descr_website" id="nx_signup_inp_website" value="<?php echo($sWebsite); ?>" class="regular-text" />
							<p class="description" id="nx_signup_descr_website"><?php _e('Base domain of your site without protocol and path', 'netroxsc'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_fname"><?php _e('First name', 'netroxsc'); ?></label>
						</th>
						<td>
							<input name="fname" type="text" id="nx_signup_inp_fname" value="<?php echo($sName); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_lname"><?php _e('Last name', 'netroxsc'); ?></label>
						</th>
						<td>
							<input name="lname" type="text" id="nx_signup_inp_lname" value="<?php echo($sName); ?>" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_pass"><?php _e('Password'); ?></label>
						</th>
						<td>
							<input name="password" type="password" id="nx_signup_inp_pass" aria-describedby="nx_signup_descr_pass" class="regular-text" />
							<p class="description" id="nx_signup_descr_pass"><?php _e('Create a secure password', 'netroxsc'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="nx_signup_inp_conf"><?php _e('Confirm'); ?></label>
						</th>
						<td>
							<input name="confirm" type="password" id="nx_signup_inp_conf" aria-describedby="nx_signup_descr_conf" class="regular-text" />
							<p class="description" id="nx_signup_descr_conf"><?php _e('Type in the password once again', 'netroxsc'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Sign Up','netroxsc') ?>"> 
				<button type="button" onclick="nxSignupSwitch(false)" id="nx_switch_signup" class="button"><?php _e("I already have a Netrox SC account", 'netroxsc') ?></button>
			</p>
		</form>
	</div>
</div>
