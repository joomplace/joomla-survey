<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

        /** Libchart - PHP chart library
        *       
        * Copyright (C) 2005-2006 Jean-Marc Tr�meaux (jm.tremeaux at gmail.com)
        *       
        * This library is free software; you can redistribute it and/or
        * modify it under the terms of the GNU Lesser General Public
        * License as published by the Free Software Foundation; either
        * version 2.1 of the License, or (at your option) any later version.
        * 
        * This library is distributed in the hope that it will be useful,
        * but WITHOUT ANY WARRANTY; without even the implied warranty of
        * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
        * Lesser General Public License for more details.
        * 
        * You should have received a copy of the GNU Lesser General Public
        * License along with this library; if not, write to the Free Software
        * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
        * 
        */
        
        /**
        * Text drawing helper
        *
        * @author   Jean-Marc Tr�meaux (jm.tremeaux at gmail.com)
        */

        class Text
        {
                var $HORIZONTAL_LEFT_ALIGN = 1;         // PHP4 doesn't support class constants
                var $HORIZONTAL_CENTER_ALIGN = 2;
                var $HORIZONTAL_RIGHT_ALIGN = 4;
                var $VERTICAL_TOP_ALIGN = 8;
                var $VERTICAL_CENTER_ALIGN = 16;
                var $VERTICAL_BOTTOM_ALIGN = 32;

                /**
                * Creates a new text drawing helper
                *
                * @access       public
                */
                
                function Text()
                {
						global $mosConfig_absolute_path;
                        // Free low-res fonts based on Bitstream Vera <http://dejavu.sourceforge.net/wiki/>
						$this->fontCondensed = $mosConfig_absolute_path . "/media/DejaVuSansCondensed.ttf";
                        $this->fontCondensedBold = $mosConfig_absolute_path . "/media/DejaVuSansCondensed-Bold.ttf";
                        //$this->fontCondensed = dirname(__FILE__) . "/../fonts/DejaVuSansCondensed.ttf";
                        //$this->fontCondensedBold = dirname(__FILE__) . "/../fonts/DejaVuSansCondensed-Bold.ttf";
                }

                /**
                * Print text
                *
                * @access       public
                * @param        Image           GD image
                * @param        integer         text coordinate (x)
                * @param        integer         text coordinate (y)
                * @param        Color           text color
                * @param        string          text value
                * @param        string          font file name
                * @param        bitfield        text alignment
                */
                
                function printText($img, $px, $py, $color, $text, $fontFileName, $align = 0, $twidth = 9999999)
                {
					    if(!($align & $this->HORIZONTAL_CENTER_ALIGN) && !($align & $this->HORIZONTAL_RIGHT_ALIGN))
                                $align |= $this->HORIZONTAL_LEFT_ALIGN;

                        if(!($align & $this->VERTICAL_CENTER_ALIGN) && !($align & $this->VERTICAL_BOTTOM_ALIGN))
                                $align |= $this->VERTICAL_TOP_ALIGN;

                        $fontSize = 8;
                        $lineSpacing = 1;
						if (function_exists('html_entity_decode')) {
							$text = @html_entity_decode($text, ENT_QUOTES, 'UTF-8'); 
						} else { 
							$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
							$trans_tbl = array_flip($trans_tbl);
							$text = strtr($text, $trans_tbl);
						}

                        list($llx, $lly, $lrx, $lry, $urx, $ury, $ulx, $uly) = @imageftbbox($fontSize, 0, $fontFileName, $text, array("linespacing" => $lineSpacing));

                        $textWidth = $lrx - $llx;
                        $textHeight = $lry - $ury;
						
						if ($textWidth > $twidth) {
							$text = sf_SafeSplit($text, intval($twidth/7));
							$textWidth = $twidth;
							
						}
						
                        $angle = 0;

                        if($align & $this->HORIZONTAL_CENTER_ALIGN)
                                $px -= $textWidth / 2;

                        if($align & $this->HORIZONTAL_RIGHT_ALIGN)
                                $px -= $textWidth;

                        if($align & $this->VERTICAL_CENTER_ALIGN)
                                $py += $textHeight / 2;

                        if($align & $this->VERTICAL_TOP_ALIGN)
                                $py += $textHeight;

                       @imagettftext($img, $fontSize, $angle, $px, $py, $color->getColor($img), $fontFileName, $text);
                }
                
                /**
                * Print text centered horizontally on the image
                *
                * @access       public
                * @param        Image           GD image
                * @param        integer         text coordinate (y)
                * @param        Color           text color
                * @param        string          text value
                * @param        string          font file name
                */
                
                function printCentered($img, $py, $color, $text, $fontFileName, $twidth=999999)
                {
				
                        $this->printText($img, imagesx($img) / 2, $py, $color, $text, $fontFileName, $this->HORIZONTAL_CENTER_ALIGN | $this->VERTICAL_CENTER_ALIGN, $twidth);
                }

				function printCentered2($img, $py, $color, $text, $fontFileName, $twidth=9999999)
                {
			
                        $this->printText($img, imagesx($img) / 3, $py, $color, $text, $fontFileName, $this->HORIZONTAL_CENTER_ALIGN | $this->VERTICAL_CENTER_ALIGN, $twidth);
                }

                /**
                * Print text in diagonal
                *
                * @access       public
                * @param        Image           GD image
                * @param        integer         text coordinate (x)
                * @param        integer         text coordinate (y)
                * @param        Color           text color
                * @param        string          text value
                */
                
                function printDiagonal($img, $px, $py, $color, $text, $fontFileName = null, $columnWidth = 0)
                {
					//echo $text.'<br/>';
			if (function_exists('html_entity_decode')) {
        			$text = @html_entity_decode($text, ENT_QUOTES, 'UTF-8'); 
    			}
			else { 
        			$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        			$trans_tbl = array_flip($trans_tbl);
        			$text = strtr($text, $trans_tbl);
    			}
				
	                $fontSize = 8;
			if ($fontFileName == null)
                      		$fontFileName = $this->fontCondensed;

                        $lineSpacing = 1;

                        list($lx, $ly, $rx, $ry) = @imageftbbox($fontSize, 0, $fontFileName, $text, array("linespacing" => $lineSpacing));
                        $textWidth = $rx - $lx;
						
						if (intval($columnWidth) > 0 && $textWidth > $columnWidth) {						
							$text = sf_SafeSplit($text, intval($columnWidth/7));
							$px = $px + 5;
						}
						elseif (intval($columnWidth) > 0)
							$px = $px + ($columnWidth - $textWidth) / 2 ;
			$angle = 0;
			
                        @imagettftext($img, $fontSize, $angle, $px, $py+3, $color->getColor($img), $fontFileName, $text);
                }
        }
?>
