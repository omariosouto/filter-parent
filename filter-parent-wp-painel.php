<?php
/*
Plugin Name: Filter Parent WP Painel
Plugin URI: https://github.com/MarioSkynex/Filter-Parent/
Description: Filter parent is a plugin that filter 'post-types' in wp-admin by their parent.
Version: 1.0
Author: Mario Skynex
Author URI: https://github.com/MarioSkynex/
License: GPL2
*/


class SKN_page_filter
{
	//Methods
    public function __construct()
    {
        add_action('restrict_manage_posts', array($this, 'filter_parent'));
        add_filter('parse_query', array($this, 'query_filter_parent'));
    }

    //Method that filter the parent post-type
    public function filter_parent()
    { 
		//Global variable that get the current WordPress wp-admin Page.
		global $pagenow;

        //Verify if the page is 'edit.php' and if you are on a post_type page
	    if ($pagenow == 'edit.php' && isset($_GET['post_type']))
	    {
	        if (isset($_GET['motherPostID'])) 
	        {
		        $dropdown_options = array(
		            'depth' => 2,
		            'hierarchical' => 1,
		            'name' => 'motherPostID',
		            'post_type' => $_GET['post_type'],
		            'selected' => $_GET['motherPostID'],
		            'show_option_none' => __( ' Filtro Todos ' ),
		            'sort_column' => 'name',
		        );
	        } 
	        else 
	        {
		        $dropdown_options = array(
		            'depth' => 2,
		            'hierarchical' => 1,
		            'name' => 'motherPostID',
		            'post_type' => $_GET['post_type'],
		            'show_option_none' => __( ' Filtro Todos ' ),
		            'sort_column' => 'name',
		        );
	        }

	        wp_dropdown_pages( $dropdown_options );
        }
    } //End filter_parent

    //Query that filter the post-type
    public function query_filter_parent($query)
    {
        if (isset($_GET['motherPostID']))
        {
	        global $pagenow;

	        $childPostType = get_pages(
	        					array(
					                'child_of' => $_GET['motherPostID'],
					                'post_status' => array('publish','draft','trash')
				               	)
	        				 );

	        $filteredPostTypes = array($_GET['motherPostID']);

	        foreach($childPostType as $cpt){
	        	array_push($filteredPostTypes, $cpt->ID);
	        }

	        $queryVars = &$query->query_vars;
	        if ($pagenow == 'edit.php' && isset($_GET['post_type']))
	        {
	            $queryVars['post__in'] = $filteredPostTypes;
	        }

        }

    } //End query_filter_parent

}//End SKN_page_filter

if(is_post_type_hierarchical($_GET['post_type']) && isset($_GET['post_type'])){
    $skn_page_filter = new SKN_page_filter();
}