<?php defined('ABSPATH') or die("Bye!"); ?>
<div id="nx_settings_container">
	<h1><img class="nx_top_logo" src="<?php echo NETROX_URL_IMG."logo.png"; ?>" alt="<?php _e('Netrox SC Live Chat Settings', 'netroxsc'); ?>" /></h1>

	<div class="card">
	<h3><?php _e('Live chat settings', 'netroxsc'); ?></h3>
	<?php 
			$iSitesCount = count($aAdminSites);
			if (!$iSitesCount): ?>
			
			<p>
				<?php _e('It seems that there are no websites attached to your Netrox SC account.', 'netroxsc'); ?>
			</p>
			<p><?php echo str_replace('##URL##', NETROX_URL_SYS."t_acc_gen", __('Please add the website at the <a href="##URL##">Account - General</a> section of Netrox SC web interface.','netroxsc')); ?></p>
		
	<?php	else: ?>

				<script type="text/javascript">
					;nxSwitchSiteBlock	= function(select) {
						var id = select.options[select.selectedIndex].value,
							allTRs = document.getElementsByClassName("nx_tr_site"),
							selected = document.getElementById("nx_tr_site_" + id);
						for (var i in allTRs) {
							if (!allTRs.hasOwnProperty(i)) continue;
							allTRs[i].style.display = "none";
						}
						selected.style.display = "table-row";
					};
				</script>

					<form method="post" action="" novalidate="novalidate">
						<input type="hidden" name="action" value="set" />

						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><?php _e('Status', 'netroxsc'); ?></th>
									<td>
										<fieldset>
											<legend class="screen-reader-text">
												<span><?php _e('Status', 'netroxsc'); ?></span>
											</legend>
											<label for="nx_inp_enable">
												<input name="enable" type="checkbox" <?php if ($this->enabled) echo "checked"; ?> id="nx_inp_enable" value="1" />
												<?php _e('Enable on site', 'netroxsc'); ?>
											</label>
										</fieldset>
									</td>
								</tr>					
						<?php 
							for ($i = 0; $i < count($aAdminSites); $i++):
								$aSite = $aAdminSites[$i];
								$bIsSelectedSite = $aSite["id"] == $this->siteID;
								// Site select only if more than 1 site
								if (count($aAdminSites) > 1): 
									// First site - opening SELECT
									if ($i == 0):	
						?>
								<tr>
									<th scope="row">
										<label for="nx_sel_siteid"><?php _e('Website', 'netroxsc'); ?></label>
									</th>
									<td>
										<select name="site_id" id="nx_sel_siteid" onchange="nxSwitchSiteBlock(this);">
						<?php		endif; 	?>
											<option value="<?php echo $aSite["id"]; ?>" <?php if ($bIsSelectedSite) echo "selected"; ?>>
												<?php echo $aSite["name"]; ?>
											</option>
						<?php		if ($i == count($aAdminSites) - 1):		?>			
										</select>
									</td>
								</tr>
						<?php		endif;
								else: ?>
								<input name="site_id" value="<?php echo $aSite["id"] ?>" type="hidden" />
						<?php	endif;							
							endfor;
								
							for ($i = 0; $i < count($aAdminSites); $i++):
								$aSite = $aAdminSites[$i];
								$bIsSelectedSite = $aSite["id"] == $this->siteID;

								// Draw blocks with widget dropdowns for each site
								$aWidgets = $aSite["chatWndThemes"];
								for ($ii = 0; $ii < count($aWidgets); $ii++):
									$aWidget = $aWidgets[$ii];
									$bIsSelectedWidget = $bIsSelectedSite && $aWidget["id"] == $this->widgetID;
									// First widget - opening SELECT
									if ($ii == 0):
						?>
								<tr class="nx_tr_site" id="nx_tr_site_<?php echo $aSite["id"]; ?>" <?php if (!$bIsSelectedSite) echo 'style="display:none;"'; ?>>
									<th scope="row">
										<label for="widget_id_<?php echo $aSite["id"]; ?>"><?php _e('Widget', 'netroxsc'); ?></label>
									</th>
									<td>
										<select name="widget_id_<?php echo $aSite["id"]; ?>" id="widget_id_<?php echo $aSite["id"]; ?>">
						<?php		endif;	?>
											<option value="<?php echo $aWidget["id"]; ?>" <?php if ($bIsSelectedWidget) echo "selected"; ?>>
												<?php echo $aWidget["id"].": ".$aWidget["name"]; ?>
											</option>
						<?php		if ($ii == count($aWidgets) - 1):	?>
										</select>
									</td>
								</tr>
						<?php
									endif;
								endfor;
							endfor; 
						?>
							</tbody>
						</table>
						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Apply settings','netroxsc') ?>"> 
							<input type="submit" name="unbind" class="button" value="<?php _e('Log in as different user', 'netroxsc') ?>"> 
						</p>
					</form>
	<?php	endif; ?>
	</div>
	<div class="card">
		<h3><?php _e('Chat with visitors', 'netroxsc'); ?></h3>
		<p><?php _e('Visitor communication is carried out via the Netrox SC web-interface. The button will open the chat zone in a new tab.', 'netroxsc'); ?>
		<p class="submit">
			<a href="<?php echo NETROX_URL_SYS."t_workdesk"; ?>" target="_blank" class="button"><?php _e('Proceed to the chat zone', 'netroxsc'); ?></a> 
		</p>
	</div>
</div>