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
        * Color
        *
        * @author   Jean-Marc Tr�meaux (jm.tremeaux at gmail.com)
        */

        class ColorHex
        {
                /**
                * Creates a new color
                *
                * @access       public
                * @param        integer         red [0,255]
                * @param        integer         green [0,255]
                * @param        integer         blue [0,255]
                * @param        integer         alpha [0,255]
                */
                
                function ColorHex($color, $alpha = 0)
                {
                        $this->red =  hexdec(substr($color, 0, 2));
                        $this->green =  hexdec(substr($color, 2, 2));
                        $this->blue =  hexdec(substr($color, 4, 2));
                        $this->alpha = (int)round($alpha * 127.0 / 255);
                        
                        $this->gdColor = null;
                }
                
                /**
                * Get GD color
                *
                * @access       public
                * @param        $img            GD image resource
                */
                
                function getColor($img)
                {
                        // Checks if color has already been allocated
                        
                        if(!$this->gdColor)
                        {
                                if($this->alpha == 0 || !function_exists('imagecolorallocatealpha'))
                                        $this->gdColor = imagecolorallocate($img, $this->red, $this->green, $this->blue);
                                else
                                        $this->gdColor = imagecolorallocatealpha($img, $this->red, $this->green, $this->blue, $this->alpha);
                        }
                        
                        // Returns GD color
                        
                        return $this->gdColor;
                }
        }
?>
