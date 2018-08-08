<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class SFPageNav {
	/** The record number to start dislpaying from
	 *  @var int */
	var $limitstart 	= null;
	/** Number of rows to display per page
	 * @var int */
	var $limit 			= null;
	/** Total number of rows
	 * @var int */
	var $total 			= null;

	var $prefix = null;

	var $viewall = false;

	function __construct( $total, $limitstart, $limit ) {
		$this->total 		= (int) $total;
		$this->limitstart 	= (int) max( $limitstart, 0 );
		$this->limit 		= (int) max( $limit, 1 );
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}
		if (($this->limit-1)*$this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}
	}
	/**
	 * @return string The html for the limit # input box
	 */
	function getLimitBox ($link) {
		global $mosConfig_list_limit;
		$limits = array();
		foreach ( array(5,10,15,20,25,50,100) as $i ) {
			$limits[] = JHtmlSelect::option( "$i" );
		}
		$limits[] = JHtmlSelect::option( '999', JText::_('JALL') );

		// build the html select list
		$link = $link ."&amp;limit='+this.options[selectedIndex].value+'&amp;limitstart=". $this->limitstart;
		//$link = sefRelToAbs( $link );
		$link = str_replace('%5C%27',"'", $link);$link = str_replace('%5B',"[", $link);$link = str_replace('%5D',"]", $link);
		return JHtmlSelect::genericlist( $limits, 'limit', 'class="inputbox" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $this->limit);

	}
	/**
	 * Writes the html limit # input box
	 */
	function writeLimitBox ($link) {
		echo $this->getLimitBox($link);
	}
	function writePagesCounter($return = false) {
		if ( !$return)
			echo $this->getPagesCounter();
		else
			return $this->getPagesCounter();
	}
	/**
	 * @return string The html for the pages counter, eg, Results 1-10 of x
	 */
	function getPagesCounter() {
		$html = '';
		$from_result = $this->limitstart+1;
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$html .= "\n".JText::_('COM_SURVEYFORCE_RESULTS')." <strong>" . $from_result . " - " . $to_result . "</strong> ".JText::_('COM_SURVEYFORCE_OF_TOTAL')." <strong>" . $this->total . "</strong>";

		} else {
			$html .= "\n".JText::_('COM_SURVEYFORCE_NO_RESULTS');
		}
		return $html;
	}
	/**
	 * Writes the html for the pages counter, eg, Results 1-10 of x
	 */
	function writePagesLinks($link) {
		echo "<ul>";
		echo $this->getPagesLinks($link);
		echo "</ul>";
	}
	/**
	 * @return string The html links for pages, eg, previous, next, 1 2 3 ... x
	 */
	function getPagesLinks($link) {
		$limitstart = max( (int) $this->limitstart, 0 );
		$limit		= max( (int) $this->limit, 1 );
		$total		= (int) $this->total;
		$html 				= '';
		$displayed_pages 	= 10;		// set how many pages you want displayed in the menu (not including first&last, and ev. ... repl by single page number.
		$total_pages = ceil( $total / $limit );
		$this_page = ceil( ($limitstart+1) / $limit );

		$start_loop = $this_page-floor($displayed_pages/2);
		if ($start_loop < 1) {
			$start_loop = 1;
		}
		if ($start_loop == 3) {
			$start_loop = 2;
		}
		if ( $start_loop + $displayed_pages - 1 < $total_pages - 2 ) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$html .= "\n<li><a href=\"". sefRelToAbs( "$link&amp;limitstart=0" ) ."\" title=\"".JText::_('COM_SURVEYFORCE_FIRST')."\">".JText::_('COM_SURVEYFORCE_FIRST')."</a></li>";
			$html .= "\n<li><a href=\"".sefRelToAbs( "$link&amp;limitstart=$page" )."\" title=\"".JText::_('COM_SURVEYFORCE_PREV')."\">".JText::_('COM_SURVEYFORCE_PREV')."</a></li>";
			if ($start_loop > 1) {
				$html .= "\n<li><a href=\"". sefRelToAbs( "$link&amp;limitstart=0" ) ."\" title=\"".JText::_('COM_SURVEYFORCE_FIRST')."\">1</a></li>";
			}
			if ($start_loop > 2) {
				$ret .= "\n<li><span> <strong>...</strong> </span></li>";
			}
		} else {
			$html .= "\n<li><span>".JText::_('COM_SURVEYFORCE_FIRST')."</span></li>";
			$html .= "\n<li><span>".JText::_('COM_SURVEYFORCE_PREV')."</span></li>";
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) {
				$html .= "\n<li><span> $i </span>";
			} else {
				$html .= "\n<li><a href=\"".sefRelToAbs( "$link&amp;limitstart=$page" )."\"><strong>$i</strong></a></li>";
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			if ($stop_loop < $total_pages-1) {
				$html .= "\n<li><span> <strong>...</strong> </span></li>";
			}
			if ($stop_loop < $total_pages) {
				$html .= "\n<li><a href=\"".sefRelToAbs( "$link&amp;limitstart=$end_page" )."\" title=\"".JText::_('COM_SURVEYFORCE_END')."\"> <strong>" . $total_pages."</strong></a></li>";
			}
			$html .= "\n<li><a href=\"".sefRelToAbs( "$link&amp;limitstart=$page" )."\" title=\"".JText::_('COM_SURVEYFORCE_NEXT')."\"> ".JText::_('COM_SURVEYFORCE_NEXT')."</a></li>";
			$html .= "\n<li><a href=\"".sefRelToAbs( "$link&amp;limitstart=$end_page" )."\" title=\"".JText::_('COM_SURVEYFORCE_END')."\"> ".JText::_('COM_SURVEYFORCE_END')."</a></li>";
		} else {
			$html .= "\n<li><span>".JText::_('COM_SURVEYFORCE_NEXT')."</span></li>";
			$html .= "\n<li><span>".JText::_('COM_SURVEYFORCE_END')."</span></li>";
		}
		return $html;
	}
	function getListFooter($link) {
		$html = '<table class="adminlist"><tr><th colspan="3" style="text-align:center;">';
		$html .= $this->getPagesLinks($link);
		$html .= '</th></tr><tr>';
		$html .= '<td nowrap="nowrap" width="48%" align="right">'.JText::_('COM_SURVEYFORCE_DISPLAY').' #</td>';
		$html .= '<td>' .$this->getLimitBox($link) . '</td>';
		$html .= '<td nowrap="nowrap" width="48%" align="left">' . $this->getPagesCounter() . '</td>';
		$html .= '</tr></table>';
		return $html;
	}
	/**
	 * Sets the vars for the page navigation template
	 */
	function setTemplateVars( &$tmpl, $link='', $name = 'admin-list-footer' ) {
		$tmpl->addVar( $name, 'PAGE_LINKS', $this->getPagesLinks($link) );
		$tmpl->addVar( $name, 'PAGE_LIST_OPTIONS', $this->getLimitBox($link) );
		$tmpl->addVar( $name, 'PAGE_COUNTER', $this->getPagesCounter() );
	}

	function isMultiPages() {
		return ($this->total > $this->limit );
	}

	function rowNumber($i) {
		return $i + 1 + $this->limitstart;
	}
}
