<?php
/**
 *	Module: Product Builder
 *
 *	index.php
 *
 *	Actions
 *  -----------------------
 *	do_add_option     : To add option
 *	do_edit_option    : To edit option
 *	do_delete_option  : To delete option
 *	do_add_builder    : To add builder
 *	do_edit_builder   : To edit builder
 *	do_delete_builder : To delte builder // This will be removed when the development is done
 *	edit_option 	  : Load the edit form for option
 *  add_option 		  : Load the add form for option
 *	view_option 	  : List all options
 *	search_option 	  : Load the search form for options
 *	edit_builder 	  : Load the edit form for builder
 *  add_builder 	  : Load the add form for builder
 *	view_builder 	  : List all builder
 *	search_builder 	  : Load the search form for builder
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

// Include Common
include_once('common.inc.php');
// Action
$action = $posted['action'];

// Initiate product builder module
$pb = new ProductBuilder($DB);

// Update Actions
switch($action) {
	case "do_add_option":
		if (isset($posted['pb_options']) === true && empty($posted['pb_options']) === false) {
			// Get option data from post
			$options = $posted['pb_options'];

			// Option from post
			$optionsFromPost = $pb->stdOptionFromPost($options,$_FILES['option_image']);

			// Get option id
			$id = $pb->addoption($optionsFromPost);

			// Record History
			$optionById = $pb->getOptionById($id);
			$pb->addHistory($optionById, $id, "options", $staff_id);
		}

		// Page redirection
		$action = "edit_option";
		$get_vars['action'] = $action;
		$get_vars['option_id'] = $id;

		$_SESSION['updated_token'] = md5(uniqid());
		$get_vars['updated'] = $_SESSION['updated_token'];
		break;

	case "do_edit_option" :
		if (isset($posted['pb_options']) === true && empty($posted['pb_options']) === false && 
			isset($posted['pb_options']['id']) === true && empty($posted['pb_options']['id']) === false) {
			// Get options data
			$options = $posted['pb_options'];

			// Option id to be updated
			$option_id = $options['id'];
			$optionsFromPost = $pb->stdOptionFromPost($options,$_FILES['option_image']); 
			// Update option data
			$pb->updateOption($optionsFromPost, $option_id);

			// Record History
			$optionById = $pb->getOptionById($option_id);
			$pb->addHistory($optionById, $option_id, "options", $staff_id);
		}

		// Page redirection
		$action = "edit_option";
		$get_vars['action'] = $action;
		$get_vars['option_id'] = $option_id;

		$_SESSION['updated_token'] = md5(uniqid());
		$get_vars['updated'] = $_SESSION['updated_token'];
		break;

	case "do_delete_option":
		if (isset($posted['option_id']) === true && empty($posted['option_id']) === false) {
			$option_id = $posted['option_id'];

			if ($pb->isOptionExist($option_id)) {
				$pb->deleteOption($option_id);
			}
		}

		$action = "view_option";
		$get_vars['action'] = $action;
		unset($get_vars['option_id']);
		break;

	case "do_add_builder":
		$error = false;

		if (isset($posted['pb_builder']) === true && empty($posted['pb_builder']) === false) {
			// Get builder data from post
			$builders = $posted['pb_builder'];
			$builders["staff"] = $staff_id;

			// Get builder from post
			$builderFromPost = $pb->stdBuilderFromPost($builders);

			foreach($builderFromPost['websites'] as $website) {
				if ($pb->isBuilderWebsiteExist($website)) {
					//$error = true;
					break;
				}
			}

			if ($error === false) {
				$id = $pb->addBuilder($builderFromPost);
			}

			// Record History
			$builderById = $pb->getBuilderById($id);
			$pb->addHistory($builderById, $id, "builders", $staff_id);
		}

		// Page redirection
		$action = "edit_builder";
		$get_vars['action'] = $action;
		$get_vars['builder_id'] = $id;

		if ($error === false) {
			$_SESSION['updated_token'] = md5(uniqid());
			$get_vars['updated'] = $_SESSION['updated_token'];
		} else {
			$_SESSION['website_err_token'] = md5(uniqid());
			$get_vars['website_err'] = $_SESSION['website_err_token'];
		}
		break;

	case "do_edit_builder":
		$error = false;
		if (isset($posted['pb_builder']) === true && empty($posted['pb_builder']) === false &&
			isset($posted['pb_builder']['id']) === true && empty($posted['pb_builder']['id']) === false) {
			$builders = $posted['pb_builder'];
			$builders["staff"] = $staff_id;

			// Builder id to be updated
			$builder_id = $builders['id'];
			$builderFromPost = $pb->stdBuilderFromPost($builders);
			foreach($builderFromPost['websites'] as $website) {
				if ($pb->isBuilderWebsiteExist($website, $builder_id)) {
					//$error = true;
					break;
				}
			}

			if ($error === false) {
				// Update builder data
				$pb->updateBuilder($builderFromPost, $builder_id);
			}

			// Record History
			$builderById = $pb->getBuilderById($builder_id);
			$pb->addHistory($builderById, $builder_id, "builders", $staff_id);
		}

		// Page Redirection
		$action = "edit_builder";
		$get_vars['action'] = $action;
		$get_vars['builder_id'] = $builder_id;

		if ($error === false) {
			$_SESSION['updated_token'] = md5(uniqid());
			$get_vars['updated'] = $_SESSION['updated_token'];
		} else {
			$_SESSION['website_err_token'] = md5(uniqid());
			$get_vars['website_err'] = $_SESSION['website_err_token'];
		}
		break;

	case "do_delete_builder":
		if (isset($posted['builder_id']) === true && empty($posted['builder_id']) === false) {
			$builder_id = $posted['builder_id'];

			if ($pb->isBuilderExist($builder_id)) {
				$pb->deleteBuilder($builder_id);
			}
		}

		$action = "view_builder";
		$get_vars['action'] = $action;
		unset($get_vars['builder_id']);
		break;

	case "unlock_option" :
		$pb->optionSetLock($posted['gid']);
		$action = "view_option";
		$get_vars['action']=$action;
		break;

	case "unlock_builder" :
		$pb->builderSetLock($posted['gid']);
		$action = "view_builder";
		$get_vars['action'] = $action;
		break;
	case "add_update_category":
		$categoryFromPost = $pb->stdCategoryFromPost($posted['category'],$_FILES['category']);
		$pb->addUpdateCategory($categoryFromPost);
	  	if (array_key_exists('item_category_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_category_deleted_val']) as $v) {
                if ($v) {
                    $pb->deleteCategory($v);
                }
            }
        }
		$action='view_category';
	break;
}

// Page Redirect
$keep_only = array('action', 'option_id', 'builder_id', 'updated', 'website_err', 'section', 'id', 'return_message', 'gid');
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value) || !in_array($key, $keep_only)) unset($get_vars[$key]);
if (count($posted['site_id'] ?? [])) $get_vars['site_id'] = $posted['site_id'];
switch($action) {
	case "edit_option" :
		// Lock option editor
		$pb->optionSetLock($posted['gid'], $_GLOBALS['staff']['id']);
	case 'add_option' :
		header("Location: addoption.php?" . http_build_query($get_vars));
		exit();
		break;

	case 'view_option' :
		header("Location: viewoption.php?" . http_build_query($get_vars));
		exit();
		break;
	
	case 'search_option' :
		header("Location: search_options.php?" . http_build_query($get_vars));
		exit();
		break;

	case "edit_builder" :
		// Lock builder editor
		$pb->builderSetLock($posted['gid'], $_GLOBALS['staff']['id']);
	case "add_builder" :
		header("Location: addbuilder.php?" . http_build_query($get_vars));
		exit();
		break;

	case 'search_builder' :
		header("Location: search_builders.php?" . http_build_query($get_vars));
		exit();
		break;

	case 'view_history' :
		header("Location: view_history.php?" . http_build_query($get_vars));
		exit();
		break;
	case 'view_category' :
		header("Location: view_category.php?" . http_build_query($get_vars));
		exit();
		break;
	case 'clone_option' : 
		$pb->optionClone($posted['gid'], $posted['option_id'],$_GLOBALS['staff']['id']);
		header("Location: viewoption.php?" . http_build_query($get_vars));
		exit();
		break;
	default :
		header("Location: viewbuilder.php?" . http_build_query($get_vars));
		exit();
		break;
}
?>
