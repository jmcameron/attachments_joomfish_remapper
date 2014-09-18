<?php
/**
 * Attachments for Joomfish library
 *
 * @package     Attachments
 *
 * @author      Jonathan M. Cameron <jmcameron@jmcameron.net>
 * @copyright   Copyright (C) 2013 Jonathan M. Cameron, All Rights Reserved
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        https://github.com/jmcameron/attachments_joomfish_remapper
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


/**
 * Attachments Remapper for Joomfish
 *
 * @package  Attachments
 *
 * @since    3.1
 */
class AttachmentsRemapper
{
	/**
	 * Remap the regular parent_id for the Joomfish translated parent_id
	 *
	 * @param  int     $parent_id  the ID of the parent (owner of the attachment)
	 * @param  string  $parent_type the parent type (eg, 'com_content')
	 * @param  string  $parent_entity the type of parent content item (eg, 'article', 'category', etc)
	 *
	 * @return int  the remapped parent_id.  Upon failure, the original parent_id is returned
	 */
	static public function remapParentID($parent_id, $parent_type, $parent_entity, $lang='')
	{
		// Only handle regular com_content articles and categories
		if ($parent_type != 'com_content')
		{
			return $parent_id;
		}
		if (!in_array($parent_entity, Array('article', 'default', 'category')))
		{
			return $parent_id;
		}

		$app = JFactory::getApplication();

		// For Joomfish, replace the parent_id with the translated ID
		$which_end = 'site';
		if ($app->isAdmin()) {
			$which_end = 'admin';
			}

		// Get the default language SEF code
		$default_lang_code = JComponentHelper::getParams('com_languages')->get($which_end,'en-GB');
		$languages = JLanguageHelper::getLanguages('lang_code');
		$default_lang_sef = $languages[$default_lang_code]->sef;

		// Update the language if via override is in request
		if (empty($lang)) {
			$lang = JRequest::getVar('lang', '');
			}
			
		// Remap the parent ID (if appropriate)
		if (($lang != '') AND ($lang != $default_lang_sef))
		{
			// Figure out which table to use
			$table = 'content';
			if ($parent_entity == 'category')
			{
				$table = 'categories';
			}

			// Do the query
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('t.translation_id')
				->from('#__jf_translationmap as t')
				->leftJoin('#__languages as lang ON lang.sef = t.language')
				->where('t.reference_table="' . $table . '"')
				->where('t.reference_id=' . (int) $parent_id);
			$db->setQuery($query);
			$result = $db->loadObjectList();

			// Make sure there were no errors
			if ( $db->getErrorNum() )
			{
				// If there is a DB error, just return the original parent_id (without complaining)
				return $parent_id;
			}
			if (count($result) != 1)
			{
				// There should only be ONE result!
				return $parent_id;
			}

			// Found the translation ID, so return it!
			return (int)$result[0]->translation_id;
		}

		return $parent_id;
	}

}