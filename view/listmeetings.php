<?php

class ListMeetingsView{ 
    
    public static function render($values){
        ?>
        
    <div class="container">
    	<h1>Mes réunions</h1>
    	
    	<?php if (sizeof($values['meetings']['owned']) > 0): ?>
        	<p>Réunions que j'ai créé</p>
        	<ul>
        	    <?php foreach ($values['meetings']['owned'] as $meeting): ?>
    				<li><a href="<?php echo BASE_URI . '/meetings/show/' . $meeting['ID_MEETING'];?>"><?php echo $meeting['SUBJECT'];?></a></li>
    			<?php endforeach ?>
        	</ul>
    	<?php endif ?>
    	
    	<?php if (sizeof($values['meetings']['participating']) > 0): ?>
        	<p>Réunions auxquelles je participe</p>
        	<ul>
        	    <?php foreach ($values['meetings']['participating'] as $meeting): ?>
                    <li><a href="<?php echo BASE_URI . '/meetings/show/' . $meeting['ID_MEETING'];?>"><?php echo $meeting['SUBJECT'];?></a></li>
                <?php endforeach ?>
        	</ul>
       <?php endif ?>
    </div>
        <?php
    }
}
?>