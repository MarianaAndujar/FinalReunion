<?php

require_once(MODEL_DIR . "/MMembers.class.php");

class ShowMeetingView{
	
	
	public static function render($values){
		?>
    <div class="container">
    	<h1>Participer</h1>
    	
    	
			<?php if(empty($values['dates'])){
				?><p>Aucune date disponible pour cette réunion</p>
			<?php
			}else{
				$participants_c = sizeof($values['participants']['uids'])
					+sizeof($values['participants']['unames']);
				?>
				<table id="poll" class="timeline">
					<tbody>
						<tr class="timeline-year">
							<th class="timeline-non-header"><div></div></th>
							<?php 
							foreach($values['dates'] as $year){
								$colspan = 0;
								foreach($year['months'] as $month){
									foreach($month['days'] as $day)
										$colspan += sizeof($day['hours']);
								}
								?>
								<th colspan="<?php echo $colspan;?>">
									<?php echo $year['year'];?>
								</th>
							<?php } ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<tr class="timeline-month">
							<th class="timeline-non-header"><div></div></th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									$colspan = 0;
									foreach($month['days'] as $day)
										$colspan += sizeof($day['hours']);
								
									?>
								<th colspan="<?php echo $colspan;?>">
									<?php echo $month['month'];?>
								</th>
							<?php }} ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<tr class="timeline-day">
							<th class="timeline-non-header"><div></div></th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									foreach($month['days'] as $day){
										$colspan = sizeof($day['hours']);
										?>
								<th colspan="<?php echo $colspan;?>">
									<?php echo $day['day'];?>
								</th>
							<?php }}} ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<tr class="timeline-hour">
							<th class="timeline-non-header">
								<div>
									<?php echo $participants_c;?> participants
								</div>
							</th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									foreach($month['days'] as $day){
										foreach($day['hours'] as $hour){
											?>
								<th colspan="1">
									<label for="id_<?php echo $hour['hour']['ID_HOURS'];?>">
										<?php echo $hour['hour']['BHOUR'] . ":00 - " 
											. (intval($hour['hour']['BHOUR']) + intval($values['meeting']['DURATION'])) 
											. ":00";?>
									</label>
								</th>
							<?php }}}} ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<?php 
						if(isset($values['participants']['uids'][0])) 
							foreach ($values['participants']['uids'][0] as $participant){ 
								if(isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] == $participant)
									continue;?>
						<tr class="timeline-input">
							<th class="timeline-non-header">
								<div><?php echo MMembers::getLoginById($participant)[0];?></div>
							</th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									foreach($month['days'] as $day){
										foreach($day['hours'] as $hour){
											$availability = array_filter($hour['availabilities'][0], 
												function($v) use($participant, $hour){
													return $v['ID_USER'] == $participant && $v['ID_HOURS'] == $hour['hour']['ID_HOURS'];
												});
											?>
								<td>
									<input type="checkbox" 
										id="id_<?php echo $hour['hour']['ID_HOURS'];?>"
										value="<?php echo $hour['hour']['ID_HOURS'];?>"
										<?php if(sizeof($availability) > 0) echo "checked";?>
									/>
								</td>
							<?php }}}} ?>
							<th class="timeline-non-header">
								<div></div>
							</th>
						</tr>
						<?php } ?>
						
						<?php if(isset($values['participants']['unames'][0])) 
							foreach ($values['participants']['unames'][0] as $participant){ ?>
						<tr class="timeline-input">
							<form method="post" action="<?php echo BASE_URI;?>/meetings/participate/<?php echo $values['meeting']['ID_MEETING'];?>">
								<th class="timeline-non-header">
									<div><input type="text" name="username" value="<?php echo $participant;?>" /></div>
								</th>
								<?php 
								foreach($values['dates'] as $year){
									foreach($year['months'] as $month){
										foreach($month['days'] as $day){
											foreach($day['hours'] as $hour){
												$availability = array_filter($hour['availabilities'][0], 
													function($v) use($participant, $hour){
														return $v['OWNER'] == $participant && $v['ID_HOURS'] == $hour['hour']['ID_HOURS'];
													});
												?>
									<td>
										<input type="checkbox" 
											id="id_<?php echo $hour['hour']['ID_HOURS'];?>"
											value="<?php echo $hour['hour']['ID_HOURS'];?>"
											<?php if(sizeof($availability) > 0) echo "checked";?>
										/>
									</td>
								<?php }}}} ?>
								<th class="timeline-non-header">
									<div><input type="submit" /></div>
								</th>
							</form
						</tr>
						<?php } ?>
						
						<tr class="timeline-input">
							<form method="post" action="<?php echo BASE_URI;?>/meetings/participate/<?php echo $values['meeting']['ID_MEETING'];?>">
								<th class="timeline-non-header">
									<div><?php 
									if(isset($_SESSION['USER_ID'])){
										echo MMembers::getLoginById($_SESSION['USER_ID'])[0];
										?><input type="hidden" name="uid" value="<?php echo $_SESSION['USER_ID'];?>" />
									<?php }else{ ?>
										<input type="text" name="username" /></div>
									<?php } ?>
								</th>
								<?php 
								foreach($values['dates'] as $year){
									foreach($year['months'] as $month){
										foreach($month['days'] as $day){
											foreach($day['hours'] as $hour){
												$availability = isset($_SESSION['USER_ID'])?array_filter($hour['availabilities'][0], 
													function($v) use($participant, $hour){
														return $v['ID_USER'] == $_SESSION['USER_ID'] && $v['ID_HOURS'] == $hour['hour']['ID_HOURS'];
													}):array();
													?>
									<td>
										<input type="checkbox" 
											id="id_<?php echo $hour['hour']['ID_HOURS'];?>"
											value="<?php echo $hour['hour']['ID_HOURS'];?>"
											<?php if(sizeof($availability) > 0) echo "checked";?>
											name="hours[]" />
									</td>
								<?php }}}} ?>
								<th class="timeline-non-header">
									<div><input type="submit" /></div>
								</th>
							</form>
						</tr>
					</tbody>
				</table>
			<?php }?>
    	</form>
    </div>
    
    <script type="text/javascript" src="<?php echo BASE_URI;?>/static/js/meeting.js"></script>
    	<?php
	}
}
?>