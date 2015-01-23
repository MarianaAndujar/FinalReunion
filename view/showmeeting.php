<?php

class ShowMeetingView{
	
	
	public static function render($values){
		?>
    <div class="container">
    	<h1>Participer</h1>
    	
    	<form method="post" 
    		action="<?php echo BASE_URI;?>/meetings/participate/<?php echo $values['meeting']['ID_MEETING'];?>">
			<?php if(empty($values['dates'])){
				?><p>Aucune date disponible pour cette réunion</p>
			<?php
			}else{?>
				<table id="poll" class="timeline">
					<tbody>
						<tr class="timeline-year">
							<th class="timeline-non-header"><div></div></th>
							<?php 
							foreach($values['dates'] as $year){
								$colspan = 0;
								foreach($year['months'] as $month){
									foreach($month['days'] as $day)
										$colspan += sizeof($day['hours'][0]);
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
										$colspan += sizeof($day['hours'][0]);
								
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
										$colspan = sizeof($day['hours'][0]);
										?>
								<th colspan="<?php echo $colspan;?>">
									<?php echo $day['day'];?>
								</th>
							<?php }}} ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<tr class="timeline-hour">
							<th class="timeline-non-header"><div>nb participants</div></th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									foreach($month['days'] as $day){
										foreach($day['hours'][0] as $hour){
											?>
								<th colspan="1">
									<label for="id_<?php echo $hour['ID_HOURS'];?>">
										<?php echo $hour['BHOUR'] . ":00 - " 
											. (intval($hour['BHOUR']) + intval($values['meeting']['DURATION'])) 
											. ":00";?>
									</label>
								</th>
							<?php }}}} ?>
							<th class="timeline-non-header"><div></div></th>
						</tr>
						
						<tr class="timeline-input">
							<th class="timeline-non-header">
								<div><input type="text" name="username" /></div>
							</th>
							<?php 
							foreach($values['dates'] as $year){
								foreach($year['months'] as $month){
									foreach($month['days'] as $day){
										foreach($day['hours'][0] as $hour){?>
								<td>
									<input type="checkbox" 
										id="id_<?php echo $hour['ID_HOURS'];?>"
										value="<?php echo $hour['ID_HOURS'];?>"
										name="hours[]" />
								</td>
							<?php }}}} ?>
							<th class="timeline-non-header">
								<div><input type="submit" /></div>
							</th>
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