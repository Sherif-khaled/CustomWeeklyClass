
<label name='test' for='<?php echo $this->html_tag_name?>' >Wordpress Users</label>
<select id ='myselect' term-id='111' class='target' name='myselect' required>
    <option value="" selected disabled hidden>Choose here</option>
             
        <?php

            use Fajr\CustomWeeklyClass\Base\Functions;
        
            foreach (Functions::get_avalibal_wp_users($this->user_role) as $user) {
               
            echo "<option ' value='" . esc_html( $user->ID ) . "'>" . $user->data->display_name . "</option>\n"; 
            }

        ?>

    </select>



