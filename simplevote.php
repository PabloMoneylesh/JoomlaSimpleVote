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
	
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)	
	{
		$position = $this->params->get('position');
		if($position=='before'){
			$html = $this->createVoting($context, &$article, &$params, $limitstart);
			return $html;
		}
		return false;
		
	}
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart)	
	{
		$position = $this->params->get('position');
		if($position=='after'){
			$html = $this->createVoting($context, &$article, &$params, $limitstart);
			return $html;
		}
		return false;
		
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
	function createVoting($context, &$article, &$params, $limitstart)	
	{	
		$categoryViewDisplay = $this->params->get('categoryViewDisplay');
		$rateNumberDisplay = $this->params->get('rateNumberDisplay');
		$rateCountDisplay = $this->params->get('rateCountDisplay');
		$vriteToArticleBody = $this->params->get('vriteToArticleBody');
		$addSeparator = $this->params->get('addSeparator');
		
		
		$view   	= JRequest::getCmd('view');
				
		$parts = explode(".", $context);

		if ($parts[0] != 'com_content')
		{
			return false;
		}
		if($view == "category" && !$categoryViewDisplay){
			return false;
		}	

		$html ="";		
		

		if (!empty($params) && $params->get('show_vote', null))
		{
			$rating = (int) @$article->rating;	
			$ratingCount = (int) @$article->rating_count;
			
			$articleId = @$article->id;
			$catid = @$article->catid;
			$artRoute = ContentHelperRoute::getArticleRoute( $articleId, $catid);
			
			$html = "<div class = 'simple_vote' itemprop='aggregateRating' itemscope itemtype='http://schema.org/AggregateRating'>";
			
			$html .=$this->createMicrodata($rating, $ratingCount);
		
			if($view == "article"){
				if($addSeparator){
					$html .= "<div class = 'separator'></div>";
				}
				$html .= "<div class = 'vote_header'><span>Оцените статью:</span></div>";
			}
			
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
				if($rateNumberDisplay){
					$voteInf = "<span>рейтинг: ". $rating ."</span>";
				}
				if($rateCountDisplay){
					$voteInf .= " <span>оценок: ". $ratingCount ."</span>";
				}
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
			if($view == "article" && $vriteToArticleBody){
				$article->text .= $html;
				return;
			}
		return $html;
		}

		return false;
	}
	
	function createMicrodata($rating, $ratingCount){
		$html = "<meta itemprop='bestRating' content='5'>";
		$html .= "<meta itemprop='worstRating' content='1'>";
		$html .= "<meta itemprop='ratingValue' content='".$rating."'>";
		$html .= "<meta itemprop='ratingCount' content='".$ratingCount."'>";
		return $html;
	}
}
