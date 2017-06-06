<?php
//template to show form for adding a new book (per user) to track it

?>

<?php if (is_user_logged_in()):?>
	<?php if (current_user_can('administrator') || current_user_can('premium') ): ?>
		<div id="form_container">
		
			<h1><a>ATM-ONLINE Book Orders Form</a></h1>
			<?php $errors = $this->validate_book_fields();?>
			<?php if (empty($errors)) 
			{
				$this->save_book_fields();
			}
			else {
				echo "<p class = \"validation_error\">" . $errors . "</p>";
			}
			?>
			<?php if (isset($_GET['submit']) && empty($errors) ) {
					echo '<p class = "form_notification">Form submitted successfully!</p>';
				}?>
			<form id="form_7941" class="appnitro" enctype="multipart/form-data" method="post" action="<?php echo $this->get_current_url();?>?submit=true">
						<div class="form_description">
				<p>Fill out this form to add a new book</p>
			</div>						
				<ul >
					<?php
					$current_user = wp_get_current_user();
					$user_email = $current_user->user_email;
					$user_display_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
					 ;?>
					<li id="li_1" >
					<label class="description" for="seller_email">Email address<span class = "required">*</span></label>
					<div>
						<input id="seller_email" name="seller_email" class="element text medium" type="email" maxlength="100" value="<?php echo $user_email;?>" required="required"/> 
					</div> 
					</li>		

					<li id="li_2" >
					<label class="description" for="shipped_to_name">Shipped To Name<span class = "required">*</span></label>
					<div>
						<input id="shipped_to_name" name="shipped_to_name" class="element text medium" type="text" maxlength="100" value="<?php echo $user_display_name;?>" required="required"/> 

					</div> 
					</li>		

					<li id="li_3" >
					<label class="description" for="trade_in_to">Trading In to<span class = "required">*</span></label>
					<div>
						<select name = "trade_in_to" required="required">
							<option value = "Amazon">Amazon</option>
							<option value = "BookScouter">BookScouter</option>
						</select>						
					</div> 
					</li>		

					<li id="li_4" >
					<label class="description" for="trn_id">Trade-In Transaction ID (TRN-ID)<span class = "required">*</span></label>
					<div>
						<input id="trn_id" name="trn_id" class="element text medium" type="text" maxlength="100" value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_5" >
					<label class="description" for="isbn13">13 Digit ISBN<span class = "required" >*</span></label>
					<div>
						<input id="isbn13" name="isbn13" class="element text medium" type="text" maxlength="13" value="" onkeyup="digits_only(this)" required="required"/> 
					</div> 
					</li>		

					<li id="li_6" >
					<label class="description" for="isbn10">10 Digit ISBN<span class = "required">*</span></label>
					<div>
						<input id="isbn10" name="isbn10" class="element text medium" type="text" maxlength="10" value="" onkeyup="digits_only(this)" required="required"/> 
					</div> 
					</li>		

					<li id="li_7" >
					<label class="description" for="book_title">Book Title<span class = "required">*</span></label>
					<div>
						<input id="book_title" name="book_title" class="element text medium" type="text" maxlength="100" value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_8" >
					<label class="description" for="edition">Edition<span class = "required">*</span></label>
					<div>
						<select name = "edition" required="required">
							<?php 
							$i = 1;
							while ($i <= 40) { ?>
								<option value = "<?php echo $i;?>"><?php echo $i;?></option>
							<?php 
							$i++;
							}
							?>
								<option value = "other" selected = "selected">Other</option>
						</select>						
					</div> 
					</li>		

					<li id="li_14" >
					<label class="description" for="book_author">Author<span class = "required">*</span></label>
					<div>
						<input id="book_author" name="book_author" class="element text medium" type="text" maxlength="100" value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_9" >
					<label class="description" for="book_cost">Cost Of Book, $<span class = "required">*</span></label>
					<div>
						<input id="book_cost" name="book_cost" class="element text medium" type="number" value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_10" >
					<label class="description" for="trd_in_value">Trade-In Value, $<span class = "required">*</span></label>
					<div>
						<input id="trd_in_value" name="trd_in_value" class="element text medium" type="number"  value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_11" >
					<label class="description" for="track_number">Tracking number<span class = "required">*</span></label>
					<div>
						<input id="track_number" name="track_number" class="element text medium" type="text" maxlength="100" value="" required="required"/> 
					</div> 
					</li>		

					<li id="li_12" >
					<label class="description" for="seller_name">Seller Name<span class = "required">*</span></label>
					<div>
						<input id="seller_name" name="seller_name" class="element text medium" type="text" maxlength="100" value="" required="required"/> 
					</div> 
					</li>	

					<li id="li_16" >
					<label class="description" for="seller_tracking_number">Seller / Rejected tracking number</label>
					<div>
						<input id="seller_tracking_number" name="seller_tracking_number" class="element text medium" type="text" maxlength="100" value=""/> 
					</div> 
					</li>							

					<li id="li_13" >
					<label class="description" for="comments">Comments</label>
					<div>
						<textarea id="book_comments" name="comments" class="element text medium"></textarea> 
					</div> 
					</li>		

					<li id="li_15" >
					<label class="description" for="pdf_uploaded">Upload a File</label>
					<div>
						<input id="pdf_uploaded" name="pdf_uploaded" class="element file" type="file"/> 
					</div>  
					</li>
						
					<li class="buttons">
				    <input type="hidden" name="form_id" value="7941" />
				    
					<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
					</li>
				</ul>
			</form>	

		</div>

	<?php else: ?>
		
			<p>Error 403. You are not authorized to view this page under current account. Please contact us.</p>
		
	<?php endif;?>

<?php else: ?>
	
		<p>You have to be logged in as a Premium User to access this page</p>
		<a href = "<?php echo wp_login_url(); ?> ">Log In/Register</a>
	
<?php endif;?>

<script type="text/javascript">
	var isbn13 = document.getElementById('isbn13'),
    cleanISBN;

	cleanISBN= function(e) {
    e.preventDefault();
    var pastedText = '';
    if (window.clipboardData && window.clipboardData.getData) { // IE
        pastedText = window.clipboardData.getData('Text');
      } else if (e.clipboardData && e.clipboardData.getData) {
        pastedText = e.clipboardData.getData('text/plain');
      }
    this.value = pastedText.replace(/\D/g, '');
};

isbn13.onpaste = cleanISBN;

var isbn10 = document.getElementById('isbn10'),
cleanISBN;
isbn10.onpaste = cleanISBN;

</script>

<script type="text/javascript">
	
function digits_only(t){
  if(t.value.match(/\D/g)){
    t.value=t.value.replace(/\D/g,'');
  }
}
</script>