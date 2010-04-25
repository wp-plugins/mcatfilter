<?php
/*
Plugin Name: mCatFilter
Plugin URI: http://www.xhaleera.com/index.php/products/wordpress-mseries-plugins/mcatfilter/
Description: Excludes categories from The Loop for display on the home page, in feeds and in archive pages.
Author: Christophe SAUVEUR
Author URI: http://www.xhaleera.com
Version: 0.4
*/

// Loading mautopopup plugin text domain
load_plugin_textdomain('mcatfilter', 'wp-content/plugins/mcatfilter/l10n');

/** \class mCatFilter
	\brief Main class
	\warning Requires PHP version 4.3.0 or later.
*/
class mCatFilter
{
	var $minWPversion = '2.1';				//!< Minimum required WP version
	var $productDomain = 'mcatfilter'; 		//!< Product domain name
	var $productName;						//!< Product name
	var $version = '0.4';					//!< Software version number
	var $categories;						//!< Excluded categories list
	var $do_not_exclude_from_tag_pages;		//!< Not excluded from tag pages flag
	
	function mCatFilter()
	{
		$this->__construct();
	}
	
	/** \brief Constructor
	*/
	function __construct()
	{
		$this->productName = __('mCatFilter', $this->productDomain);
		
		$this->setup_plugin();
		$this->load_options();
		$this->compute_post();
	
		add_action('admin_menu', array(&$this, 'create_admin_page'));
		
		if ($this->installation_complete())
		{
			add_action('pre_get_posts', array(&$this, 'filter_categories'));
			add_action('delete_category', array(&$this, 'update_on_category_removal'));
			add_action('create_category', array(&$this, 'update_on_category_update'));
			add_action('add_category', array(&$this, 'update_on_category_update'));
			add_action('edit_category', array(&$this, 'update_on_category_update'));
			add_action('edit_category_form', array(&$this, 'category_form_controls'));
			add_filter('plugin_action_links', array(&$this, 'plugin_action_links_filter'), 10, 2);
		}
	}

	/** \brief Creates sub-page in the Options menu.
	*/
	function create_admin_page()
	{
		add_menu_page($this->productName, $this->productName, 'manage_options', $this->productDomain, array(&$this, 'options_page'));
	}

	/** \brief Tells if the installation of the plugin has been correctly done.
		\return true if the plugin is correctly installed, false either.
	*/
	function installation_complete()
	{
		return (version_compare($this->version, get_option('mcatfilter_version')) == 0);
	}
	
	function cat_value_filter($value)
	{
		return (!empty($value) && preg_match("/^[1-9][0-9]*$/", $value));
	}	
	
	function is_excluded($cat_ID)
	{
		return in_array($cat_ID, $this->categories);
	}
	
	function remove_category($cat_ID)
	{
		// Refreshing options from database
		$this->load_options();
		
		// Removing deleted category ID from excluded categories list if applying
		if ($this->is_excluded($cat_ID))
		{
			$this->categories = array_diff($this->categories, array($cat_ID));
			$this->save_options();
		}
	}
	
	function add_category($cat_ID)
	{
		// Refreshing options from database
		$this->load_options();
		
		// Adding category ID to excluded categories list if not already present
		if (!$this->is_excluded($cat_ID))
		{
			$this->categories[] = $cat_ID;
			sort($this->categories, SORT_NUMERIC);
			$this->save_options();
		}
	}
	
	/** \brief Loads the plugin options from the WordPress options repository or sets them to their default if absent.
	*/
	function load_options()
	{
		$options = unserialize(get_option('mcatfilter_options'));
		
		$this->categories = $options->categories;
		$this->do_not_exclude_from_tag_pages = $options->do_not_exclude_from_tag_pages;
	}
	
	function save_options() {
		$options = (object) array('do_not_exclude_from_tag_pages' => $this->do_not_exclude_from_tag_pages,
								  'categories' => $this->categories);
		update_option('mcatfilter_options', serialize($options));
	}
	
	function options_page()
	{
		// **
		//	Unsupported WP version
		// **
		if (version_compare($GLOBALS['wp_version'], $this->minWPversion, '<'))
		{
?>
<div class="wrap">
<h2><?php _e('Unsupported WordPress version', $this->productDomain); ?></h2>
<p><?php printf(__('This plugin requires WordPress %s or later version.', $this->productDomain), $this->minWPversion); ?></p>
</div>
<?php
			return;
		}

		// **
		//	Display error and confirmation messages
		// **
		$this->display_messages();
		
		// **
		//	Header
		// **
?>
<div class="wrap">
<h2><?php echo $this->productName; ?></h2>
<form name="<?php echo $this->productDomain; ?>_global_setup" action="" method="post" id="global_setup_form">
<input type="hidden" name="form_name" value="<?php echo $this->productDomain; ?>_global_setup" />
<table class="form-table">
<tr>
<th scope="row" nowrap="nowrap"><label for="do_not_tag_exclude"><?php _e('Do not exclude from tag pages', $this->productDomain); ?></label></th>
<td><input type="checkbox" name="do_not_tag_exclude" id="do_not_tag_exclude" <?php if ($this->do_not_exclude_from_tag_pages) echo 'checked="checked"'; ?>/></td>
</tr>
</table>
<h3><?php _e('Select for exclusion', $this->productDomain); ?></h3>
<br class="clear" />
<table class="widefat">
<thead>
<tr>
<th scope="col" class="check-column">&nbsp;</th>
<th scope="col"><?php _e('Name', $this->productDomain); ?></th>
<th scope="col"><?php _e('Description', $this->productDomain); ?></th>
<th scope="col" class="num"><?php _e('Posts', $this->productDomain); ?></th>
</tr>
</thead>
<tbody id="the-list" class="list:cat">
<?php
		$theCategories = get_categories('hide_empty=0');
		
		$alt = '';
		foreach ($theCategories as $cat)
		{
			$alt = empty($alt) ? ' class="alternate"' : '';
?>
<tr<?php echo $alt; ?>>
<th scope="row" class="check-column"><input type="checkbox" name="select[]" value="<?php echo $cat->cat_ID; ?>" <?php if ($this->is_excluded($cat->cat_ID)) echo 'checked '; ?>/></td>
<td><?php if ($cat->category_parent != 0) echo '&#8212;  '; ?><strong><?php echo $cat->cat_name; ?></strong></td>
<td><?php echo $cat->category_description; ?></td>
<td><?php echo $cat->category_count; ?></td>
</tr>
<?php
		}
?>
</tbody>
</table>
<div class="tablenav">
<div class="alignleft">
<input type="submit" value="<?php _e('Save changes', $this->productDomain); ?>" name="deleteit" class="button-secondary delete" />
</div>
<br class="clear" />
</div>
</form>
</div>
<?php
	}
	
	/** \brief Displays well-formatted error and confirmation messages using default WordPress admin style sheet.
	*/
	function display_messages()
	{
		if (!empty($this->errorMessage))
			echo "<div id=\"message\" class=\"error\"><p>{$this->errorMessage}</p></div>\n";
		if (!empty($this->confirmMessage))
			echo "<div id=\"message\" class=\"updated fade\"><p>{$this->confirmMessage}</p></div>\n";
	}
	
	/** \brief Processes POST values sent by the multiples forms of the plugin configuration pages.
	*/
	function compute_post()
	{
		if (!empty($_POST['form_name']) && $_POST['form_name'] == $this->productDomain.'_global_setup')
		{
			if (empty($_POST['select']))
				$_POST['select'] = array();
			$this->categories = array_map('intval', array_filter($_POST['select'], array(&$this, 'cat_value_filter')));
			sort($this->categories, SORT_NUMERIC);
			
			$this->do_not_exclude_from_tag_pages = isset($_POST['do_not_tag_exclude']);
			
			$this->save_options();
			$this->confirmMessage = __('Chages have been succesfully saved.', $this->productDomain);
		}
		
		$this->load_options();
	}
	
	function setup_plugin()
	{
		$installed_version = get_option('mcatfilter_version');
		
		if ($installed_version === false)
		{
			update_option('mcatfilter_version', $this->version);
			
			$obj = (object) array('do_not_exclude_from_tag_pages' => true, 'categories' => array());
			update_option('mcatfilter_options', serialize($obj));
		}
		else if (version_compare($installed_version, $this->version) < 0)
		{
			$categories = get_option('mcatfilter_categories');
			$obj = (object) NULL;
			if (!empty($categories))
			{
				$categories = array_map('intval', explode(',', $categories));
				$categories = array_filter($categories, array(&$this, 'cat_value_filter'));
				$obj->categories = $categories;
			}
			else
				$obj->categories = array();
			$obj->do_not_exclude_from_tag_pages = true;
						
			delete_option('mcatfilter_categories');
			update_option('mcatfilter_options', serialize($obj));
			update_option('mcatfilter_version', $this->version);
		}
	}
	
	/** \brief Category filter
	*/
	function filter_categories($wp_query)
	{
		if ($this->do_not_exclude_from_tag_pages && is_tag())
			return;
		if (!empty($this->categories) && (is_home() || is_feed() || (is_archive() && !is_category())))
			$wp_query->query_vars['cat'] = implode(',', array_map(create_function('$val', 'return "-{$val}";'), $this->categories));
	}
	
	function update_on_category_removal($removed_cat_ID)
	{
		$this->remove_category($removed_cat_ID);
	}
	
	function update_on_category_update($updated_cat_ID)
	{	
		if (!empty($_POST['action']) && preg_match('/^(add-?|edited)cat$/', $_POST['action']))
		{
			if (empty($_POST['mcatfilter_exclude']))
				$this->remove_category($updated_cat_ID);
			else
				$this->add_category($updated_cat_ID);
		}
	}
	
	function category_form_controls()
	{
		global $cat_ID;
	
		$isChecked = (!empty($cat_ID) && $this->is_excluded($cat_ID)) ? 'checked' : '';
		
		if (version_compare($GLOBALS['wp_version'], '2.7', '<'))
		{
?>
<table class="form-table editform">
<tr class="form-field">
<th scope="row"><?php _e('mCatFilter additional options', $this->productDomain); ?></th>
<td><input type="checkbox" name="mcatfilter_exclude" id="mcatfilter_exclude" <?php echo $isChecked; ?> /> <label for="mcatfilter_exclude"><?php _e('Select this category for exclusion', $this->productDomain); ?></label></td>
</tr>
</table>
<?php
		}
		else
		{
?>
<h3><?php _e('mCatFilter additional options', $this->productDomain); ?></h3>
<table class="form-table"><tbody><tr><td><fieldset><label for="mcatfilter_exclude"><input type="checkbox" name="mcatfilter_exclude" id="mcatfilter_exclude" <?php echo $isChecked; ?> /> <?php _e('Select this category for exclusion', $this->productDomain); ?></label></fieldset></td></tr></tbody></table></div>
<?php
		}
	}
	
	function plugin_action_links_filter($links, $plugin)
	{
		static $this_plugin;
		if (!$this_plugin)
			$this_plugin = plugin_basename(__FILE__);
			
		if ($plugin == $this_plugin)
			$links = array_merge(array('<a href="admin.php?page=mcatfilter">'.__('Setup', $this->productDomain).'</a>'), $links);
		
		return $links;
	}
}

/** \brief Plugin launch function
*/
if (!function_exists('mCatFilter_launch'))
{
	function mCatFilter_launch()
	{
		$GLOBALS['__mCatFilter'] = new mCatFilter();
	}
	
	// Loading plugin after all have been loaded
	add_action('plugins_loaded', 'mCatFilter_launch');
}

// Uninstalling plugin - WP 2.7+
if (function_exists('register_uninstall_hook') && !function_exists('mCatFilter_uninstall'))
{
	function mCatFilter_uninstall()
	{
		delete_option('mcatfilter_version');
		delete_option('mcatfilter_options');
	}
	
	register_uninstall_hook(__FILE__, 'mCatFilter_uninstall');
}
?>