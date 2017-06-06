<?php get_header(); ?>

<?php
global $trd_amazon_tracker;
if (isset($_GET['amazon_trdin_search'])) {
	$search_term = $_POST['amazon_asin'];
	echo $search_term;
}
?>


<div class="banner">
	<div class="banner-content">
		<div class="tracking-form">
			<p class="title">Get Tracking Now!</p>
			<p>Maecenas feugiat metus eu ex ornare, at aliquam dui volutpat. Ut facilisis turpis sit amet pellentesque euismod.</p>
			
			<form action="?amazon_trdin_search=true" method="post" class="searchform">
				<input type="text" name="amazon_asin" placeholder="Enter your ASIN, Amazon link or book ISBN" class="s">
				<input type="submit" name="" value="Look Up" class="submit">
			</form>
		</div>
	</div>
</div>


<main>

	<div class="main-content">
	 
		<div class="items-grid about">
			<div class="item">
				<p class="title"><span>About</span> Trade In Price</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non quam condimentum leo malesuada consectetur.</p>
				<p><a href="/about/" class="more-link">Details</a></p>
			</div>
		
			<div class="item bg">
				<p>01 Amazon Trade-In Alerts</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non quam condimentum leo malesuada consectetur.</p>
			</div>
		
			<div class="item bg">
				<p>02 Amazon Trade-In History</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non quam condimentum leo malesuada consectetur.</p>
			</div>
		
			<div class="item bg">
				<p>03 Free!</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non quam condimentum leo malesuada consectetur.</p>
			</div>
		</div>
	
	</div>
</main>

<main class="welcome">

	<div class="main-content">
	 
		<article>
			<p class="title">Welcome to Trade in Price!</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tincidunt libero vitae blandit ornare. Integer dapibus sagittis enim, vel cursus elit luctus eget. Suspendisse justo dolor, efficitur vitae pharetra vitae, laoreet eget felis. In urna quam, consectetur eu orci quis, convallis pellentesque dui. Vestibulum turpis neque, tincidunt et laoreet sit amet, interdum laoreet ante.</p>
		
		</article>
	 
	</div>
</main>


<main>

	<div class="main-content">
	
		<div class="trade-in">
		
			<div class="yellow-title">Amazon trade-in top risers and fallers</div>
			
			<div class="title">Popular Risers</div>
			
			<div class="items-grid">
			
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-blue.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Blue</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-yellow.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Yellow</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
			</div>
			
			<div class="title">Today's Risers</div>
			
			<div class="items-grid">
			
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-blue.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Blue</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-yellow.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Yellow</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="up">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
			</div>
			
			<div class="title">Today's Fallers</div>
			
			<div class="items-grid">
			
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="down">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-blue.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Blue</h2>
					</a>
					<div class="down">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl-yellow.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Yellow</h2>
					</a>
					<div class="down">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
				
				<div class="item">
					<a href="">
						<div class="item-thumbnail">
							<img src="http://isbn.test-depot.info/wp-content/uploads/2017/02/jbl.jpg" alt="">
						</div>
						<h2>Portable speaker JBL Flip 3 Teal</h2>
					</a>
					<div class="down">
						<p>Up $432.73</p>
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</div>
					<div class="amount">Trade: $432</div>
				</div>
			</div>
			
			
		</div>
	</div>

</main>



<?php get_footer(); ?>

