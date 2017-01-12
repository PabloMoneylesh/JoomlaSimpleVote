<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.vote
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Vote plugin.
 *
 * @since  1.5
 */
class PlgContentSimplevote extends JPlugin
{
	function onContentAfterDisplay1($context, &$article, &$params, $limitstart)
	{
		
		
		return '<p>resultHERE</p>' ;
	}
	
	public function onContentPrepare($context, &$row, &$params, $page = 0)
{
    $document = JFactory::getDocument();
    $document->addStyleSheet("plugins/content/simplevote/simplevote.css");
	$document->addScript("plugins/content/simplevote/simplevote.css");
}

	/**
	 * Displays the voting area if in an article
	 *
	 * @param   string   $context  The context of the content being passed to the plugin
	 * @param   object   &$row     The article object
	 * @param   object   &$params  The article params
	 * @param   integer  $page     The 'page' number
	 *
	 * @return  mixed  html string containing code for the votes if in com_content else boolean false
	 *
	 * @since   1.6
	 */
	public function onContentAfterDisplay($context, &$row, &$params, $page=0)
	{
		$parts = explode(".", $context);

		if ($parts[0] != 'com_content')
		{
			return false;
		}

		$html = '';

		if (!empty($params) && $params->get('show_vote', null))
		{
			// Load plugin language files only when needed (ex: they are not needed if show_vote is not active).
			$this->loadLanguage();

			$rating = (int) @$row->rating;			

			$view = JFactory::getApplication()->input->getString('view', '');
			$img = '';

			$voteDiv = "<div class='vote_stars'>";
			
			// Look for images in template if available
			$starImageOn  = JHtml::_('image', 'system/rating_star.png', JText::_('PLG_VOTE_STAR_ACTIVE'), $imgAttribs, true);
			$starImageOff = JHtml::_('image', 'system/rating_star_blank.png', JText::_('PLG_VOTE_STAR_INACTIVE'), $imgAttribs, true);
			var_dump($starImageOn);
			

			for ($i = 0; $i < $rating; $i++)
			{
				
				$voteDiv .= "<a class='vote_star_active' value='1' onclick='submitVote(" . $i+1 . ");'></a>";
			}

			for ($i = $rating; $i < 5; $i++)
			{
				$voteDiv .= "<a class='vote_star' value='1' onclick='submitVote(" . $i+1 . ");'></a>";
			}
			$voteDiv .= "<span>". $rating ."</span>";
			$voteDiv .= "</div>";
			
			$voteForm = "<form id='vote_form' method='post' action='http://www.phototravel.dp.ua/hitcount=0' class='form-inline'>";
			$voteForm .= "<input type='hidden' name='user_rating' id='user_rating' value='0' />";
			$voteForm .= "<input type='hidden' name='url' value='http' />";
			$voteForm .= "</form>";

			$html .= $voteDiv . $voteForm;
		}

		return $html;
	}
}
