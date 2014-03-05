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
		$catid =  $params->get('catid', array());
		$order =  $params->get('ordering');
		//$auto_slide =  $params->get('auto_slide');
		$limit = $params->get('limit');
		$appParams = $app->getParams();
		

		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$sql = '('.$db->quoteName('introtext')." REGEXP '<img[^>]+>' OR ".$db->quoteName('fulltext'). " REGEXP '<img[^>]+>')";
		$sql .=  " AND state=1 AND catid IN ('".implode("','",$catid)."') ";

// Create a new query object.
		$query = $db->getQuery(true);
 
// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title', 'introtext', 'fulltext', 'catid', 'alias')));
		$query->from($db->quoteName('#__content'));
		$query->where($sql);

	    if($order == 'c_dsc'){
			$query->order($db->quoteName('created') . ' DESC');	    			     
		} else{ 			 
			$query->order($db->quoteName('modified') . ' DESC');
		}
																													     



// Reset the query using our newly populated query object.
		$db->setQuery($query, 0, $limit);
		$db->execute();

		/*$num_rows = $db->getNumRows();
		print_r("<p>tenemos ".$num_rows."renglones </p>");*/
 
// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$items = $db->loadObjectList();



		foreach ($items as &$item)
		{
			$item->slug = $item->id . ':' . $item->alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

			$data = ($item->fulltext == '') ? $item->introtext : $item->fulltext;
			preg_match_all('/(img|src)\=(\"|\')[^\"\'\>]+/i', $data, $media);
			unset($data);
			$data=preg_replace('/(img|src)(\"|\'|\=\"|\=\')(.*)/i',"$3",$media[0]);
			//print_r($data);
			$item->image = $data[0];

		}
		print_r ($items);
		return $items;
	}
}
