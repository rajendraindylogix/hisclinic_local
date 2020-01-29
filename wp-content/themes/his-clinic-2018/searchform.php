<?php
	global $search_placeholder;

	$placeholder = (!empty($search_placeholder)) ? $search_placeholder : 'Search';
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
	<label>
		<input type="search" class="search-field" placeholder="<?php echo $placeholder ?>" value="<?php echo get_search_query() ?>" name="s" />
	</label>
	<input type="submit" class="search-submit" value="Search" />
</form>