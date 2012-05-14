<?php
/*
 * add a surcharge options with the ticket display table
 * runs a javascript to add the surchage amount with the total amount
 * */
 
class EM_Surcharge{
	/*
	 * conatins all the hooks
	 * */
	 static function init(){
		 //add extra column
		add_filter('em_booking_form_tickets_cols', array(get_class(), 'set_ticket_collumns'), 10, 2);
		add_action('em_booking_form_tickets_col_surcharge', array(get_class(), 'em_booking_form_tickets_col_surchage'), 10, 2);
		
		//hook into booking submission to add discount and and surcharge details		
		add_filter('em_booking_get_post', array(get_class(), 'surcharge_booking_get_post'), 100, 2);
		add_filter('em_booking_validate', array(get_class(), 'surcharge_booking_validate'), 100, 2);
		add_filter('em_booking_save', array(get_class(), 'surcharge_booking_save'), 100, 2);
		
		//filtering the price
		add_filter('em_booking_get_prices', array(get_class(), 'get_prices'), 100, 2);
		
		//add ajax response for coupon code queries
	//	add_action('wp_ajax_surcharge_check', array(get_class(), 'surcharge_check'));
	//	add_action('wp_ajax_nopriv_surcharge_check', array(get_class(), 'surcharge_check'));
		//add css for coupon field
		//add_action('wp_head', array(get_class(), 'wp_head'));
		 //javascript
		add_action('wp_footer', array(get_class(), 'wp_footer') );
		 
	 }
	 
	 /*
	  * add an extra colum for surchage option
	  * */
	  static function set_ticket_collumns($collumns, $EM_Event){
		$collumns['surcharge'] = 'Available Room';
		 return $collumns;
	  }
	  
	  //populates the available room
	  static function em_booking_form_tickets_col_surchage($EM_Ticket, $EM_Event){
		 // var_dump($EM_Ticket);
		  ?>
		<td class="em-bookings-ticket-table-surcharge">
			<?php
				echo self::get_surcharge_options($EM_Ticket, $EM_Event);
			?>
		</td>
		<?php
	  }
	  
	  
	  // if the booking is sved
	  static function surcharge_booking_get_post($result, $EM_Booking){
		  /*
		  if( isset($_REQUEST['surcharge_tickets']) && is_array($_REQUEST['surcharge_tickets']) && ($_REQUEST['surcharge_tickets'])){
			  $booking = $EM_Booking->get_tickets_bookings();
			  $ticket_bookings = $booking->get_ticket_bookings();
		  }
		   * */
		   
		  return $result;
	  }
	 
	  //validate the booking
	  static function surcharge_booking_validate($result, $EM_Booking){
		return $result;  
	  }
	  
	  //save surcharge meta into the datbase]
	  static function surcharge_booking_save($result, $EM_Booking){
		  return $result;
	  }
	  
	  
	  static function wp_footer(){
		  ?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.surcharge-ticket-select').change(function(){
					var ticket_id = '#' + $(this).attr('id').replace('surcharge', 'em');
					var space = $(this).val() * 2;
					$(ticket_id).val(space);
				});
				
				$('.em-ticket-select').change(function(){
					var ticket_id = '#' + $(this).attr('id').replace('em', 'surcharge');					
					$(ticket_id).val(0);
				});
			});
		</script>
		<?php
	  }
	  
	  
	  static function surcharge_check(){
		if( !empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'booking_add') && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'surcharge_check' ){ //only run this when booking add form submitted and action is modified
			$result = array('result'=>false, 'message'=> __('Not available', 'em-pro'));
			
			
			/*
			if(!empty($_REQUEST['event_id'])){
				$EM_Event = new EM_Event($_REQUEST['event_id']);
				foreach($EM_Event->coupons as $EM_Coupon){
					if( $EM_Coupon->coupon_code == $_REQUEST['coupon_code'] ){
						if( $EM_Coupon->is_valid() ){
							$result['result'] = true;
							$result['message'] = $EM_Coupon->get_discount_text();
						}else{
							$result['message'] = __('Coupon Invalid','em-pro');
						}
						break;
					}
				}
			 * 
			 */
			}
			
			echo EM_Object::json_encode($result);
			exit();
		}
		
		/*
		 * returns the surcharge options
		 */
		static function get_surcharge_options($EM_Ticket, $EM_Event, $echo=true){
			$available_spaces = $EM_Ticket->get_available_spaces();
			if($available_spaces < 2) return __('not available');
			$available_rooms = floor((int)$available_spaces/2);
			if( $EM_Ticket->is_available() ) {
				$select = '<select name="surcharge_tickets['.$EM_Ticket->ticket_id.'][rooms]" class="surcharge-ticket-select" id="surcharge-ticket-spaces-' . $EM_Ticket->ticket_id .'">';
				for($i=0; $i<=$available_rooms;$i++){
					$select .= '<option value="'.$i.'">'.$i.'</option>';
				}
				$select .= '</select>';
			}
			
			return $select;
		}
		
		/*
		 * filters the prices
		 */
		static function get_prices($price, $EM_Ticket_Booking){
			//exit;
			//$price = $price - $price/2;
			
			 if( isset($_REQUEST['surcharge_tickets']) && is_array($_REQUEST['surcharge_tickets']) && ($_REQUEST['surcharge_tickets'])){
				if(isset($_REQUEST['surcharge_tickets'][$EM_Ticket_Booking->ticket_id]) && is_array($_REQUEST['surcharge_tickets'][$EM_Ticket_Booking->ticket_id])){
					$rooms = $_REQUEST['surcharge_tickets'][$EM_Ticket_Booking->ticket_id]['rooms'];
					$spaces = 2 * $rooms;
					if($spaces == $EM_Ticket_Booking->get_spaces()){
						
						$price = $price - $price/4;
					}
				}
			 
			 
			}
			
			return $price;
		}
		  
	  
}
