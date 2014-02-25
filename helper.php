<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_banners
 *
 * @package     Joomla.Site
 * @subpackage  mod_banners
 * @since       1.5
 */
require_once JPATH_SITE . '/components/com_content/helpers/route.php';
class ModBannersHelper
{
	public static function &getList(&$params)
	{
		$db = JFactory::getDbo();
		
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		

		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

// Create a new query object.
		$query = $db->getQuery(true);
 
// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title', 'introtext', 'fulltext', 'catid', 'alias')));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('introtext')." REGEXP '<img[^>]+>' OR ".$db->quoteName('fulltext'). " REGEXP '<img[^>]+>'");
		$query->order('ordering ASC');

// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		/*$num_rows = $db->getNumRows();
		print_r("<p>tenemos ".$num_rows."renglones </p>");*/
 
// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();



		/*foreach ($items as &$item)
		{
			$item->slug = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}*/

		$items=$results;


		return $items;
	}
}
