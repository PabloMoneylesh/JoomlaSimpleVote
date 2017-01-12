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
	public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet("plugins/content/simplevote/simplevote.css");
		$document->addScript("plugins/content/simplevote/simplevote.js");
	}

	/**
	 * Displays the voting area if in an article	 
	 *
	 * There is two display options - in article view and in categori. In categori voew there is less informations displayed. 
	 *	 
	 * @param   string   $context  The context of the content being passed to the plugin
	 * @param   object   &$article     The article object
	 * @param   object   &$params  The article params
	 * @param   integer  $page     The 'page' number
	 *
	 * @return  mixed  html string containing code for the votes if in com_content else boolean false
	 *
	 * @since   1.6
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart)	
	{
		
		$parts = explode(".", $context);

		if ($parts[0] != 'com_content')
		{
			return false;
		}
		$view   	= JRequest::getCmd('view');

		$html = "<div class = 'simple_vote'>";
		if($view == "article"){
			$html .= "<div class = 'separator'></div>";
			$html .= "<div class = 'vote_header'><span>Оцените статью:</span></div>";
		}
		$articleId = @$article->id;
		$catid = @$article->catid;
		$artRoute = ContentHelperRoute::getArticleRoute( $articleId, $catid);
		

		if (!empty($params) && $params->get('show_vote', null))
		{

			$rating = (int) @$article->rating;	
			$ratingCount = (int) @$article->rating_count;

			$voteDiv = "<div class='vote_stars'>";

			for ($i = 0; $i < $rating; $i++)
			{				
				$voteDiv .= "<div class='vote_star_active' value='1' onclick='submitVote(" . ($i+1) . ", ". $articleId.");'></div>";
			}
			for ($i = $rating; $i < 5; $i++)
			{
				$voteDiv .= "<div class='vote_star' value='1' onclick='submitVote(" . ($i+1) . ", ". $articleId. ");'></div>";
			}			
			$voteDiv .= "</div>";
			if($view == "article"){
				$voteInf = "<span>рейтинг: ". $rating ."</span>";
				$voteInf .= " <span>оценок: ". $ratingCount ."</span>";
			}
			
			$uri = JUri::getInstance($artRoute);
			$uri->setVar('hitcount', '0');
			$currenturi = JUri::getInstance();
			
			$voteForm = "<form id='vote_form".$articleId."' method='post' action='". $uri ."' class='form-inline'>";
			$voteForm .= "<input type='hidden' name='user_rating' id='user_rating' value='0' />";
			$voteForm .= "<input type='hidden' name='url' value='". $currenturi ."' />";
			$voteForm .= "<input type='hidden' name='task' value='article.vote'>";
			$voteForm .= JHtml::_('form.token');
			$voteForm .= "</form>";

			$html .= $voteDiv . $voteInf. $voteForm;
			$html .= "</div>";
			if($view == "article"){
				$article->text .= $html;
				return;
			}
		}

		return $html;
	}
}
