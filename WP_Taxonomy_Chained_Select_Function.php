add_shortcode('WP_Taxonomy_Chained_Select','WP_Taxonomy_Chained_Select_Function');
function WP_Taxonomy_Chained_Select_Function($atts) {
	$output = '';
	// SET ALL THE ARGUMENTS THAT MATTER,
	// USING THE $atts ARRAY IF PRESENT, 
	// OR DEFAULTS SET BELOW
	
	// set custom taxonomy name or use Categories
	$taxonomy = $atts['taxonomy'] ? : 'category';
	// set CSS id of select element, use taxonomy name if empty
	$id = $atts['id']?:$taxonomy;
	// set CSS class of select element, use taxonomy name if empty
	$class = $atts['class']?:$taxonomy;
	// set input name, use taxonomy name if empty
	$name = $atts['name']?:$taxonomy.'[]';
	// set sort orderby, use name if empty
	$orderby = $atts['orderby']?:'name';
	// set sort order, use ASC if empty
	$order = $atts['order']?:'ASC';
	// set hide_empty, use false if empty
	$hide_empty = $atts['hide_empty']?:false;
	// show_option_none text or use taxonomy name stripped of dashes and underscores, each word capitalized
	$show_option_none = $atts['show_option_none']?: 'Select '.ucwords(str_replace(array('-','_'),' ',$taxonomy));
	// allow selected element to be specified, else use show_option_none
	$sel = $atts['selected']?:'';
	$show_option_none_sub = $atts['show_option_none_sub']?:false;
	

$output .= '<div id="my_'. $id .'">';

	$args = array(
	    'show_option_none' 	=> $show_option_none,
	    'orderby'            => $orderby, 
	    'order'              => $order,
	    'name'               => $name,
	    'hierarchical'       => 0, // hierarchical is presumed
	    'parent'			=> 0, // always top level of hierarchy
	    'echo'			=> 0, // no, we want to play with it as a variable first
	    'class'              => $class,
	    'id'              	=> $id,
	    'selected'			=> $sel,
	    'taxonomy'			=> $taxonomy,
	    'hide_empty'		=> $hide_empty
	);
	$dropdown = wp_dropdown_categories( $args );
	$strSearch = '<select ';
	$strReplace ='<select onchange="jQuery(\'.select_sub.'.$id.'\').hide(); jQuery(\'#sub\'+this.options[this.selectedIndex].value).toggle(); jQuery(\'select.'.$taxonomy.'_func\').prop(\'selectedIndex\', 0);"';
	$dropdown = str_replace($strSearch,$strReplace,$dropdown);
	$output .= $dropdown;
	$parent = get_terms( $taxonomy, array ('parent'=> 0,'hide_empty'=> false, 'orderby'=>$orderby, 'order'=> $order) );
	foreach ($parent as $term ) {
		if(count(get_terms( $taxonomy, array ('parent'=> $term->term_id,'hide_empty'=> false, 'orderby'=>$orderby, 'order'=> $order) )) > 0) {
			$output .= '
			<div class="select_sub '. $id .'" id="sub'. $term->term_id .'" style="display:none;">';
			$dropdown = wp_dropdown_categories( array('taxonomy'=> $taxonomy,'show_option_none' 	=> $show_option_none_sub?:'Select '.$term->name.' Focus','name'=> $name,'parent'=> $term->term_id,'id'=>'sel'.$term->term_id,'hide_empty'=> false,'echo'=>0,'orderby'=>$orderby,'order'=>$order) );
			$dropdown = str_replace("class='postform' >","class='".$taxonomy."_func'>",$dropdown);
			$output .= $dropdown."	</div>";
		}

	}
	$output .= "</div>
	<script>
	function ".$taxonomy."() {
	  jQuery('#my_".$id." select option:selected').each(function () {
		 jQuery('#".$id." select').prop('selectedIndex', 0);
		 jQuery('.select_sub').hide();
		 });
	}
	</script>";
	
	return $output;
}
