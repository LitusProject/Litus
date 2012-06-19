<?xml version="1.0" encoding="iso-8859-1"?>

<!-- =========================================================== -->
<!--                                                             -->
<!-- (c) 2000 - 2003, Andriy Palamarchuk, Nikolai Grigoriev      -->
<!--                                                             -->
<!-- Permission is granted to use this document, copy and        -->
<!-- modify free of charge, provided that every derived work     -->
<!-- bear a reference to the present document.                   -->
<!--                                                             -->
<!-- This document contains a computer program written in        -->
<!-- XSL Transformations Language. It is published with no       -->
<!-- warranty of any kind about its usability, as a mere         -->
<!-- example of XSL technology. The author shall not assume any  -->
<!-- liability for any damage or loss of data caused by use      -->
<!-- of this program.                                            -->
<!--                                                             -->
<!-- =========================================================== -->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:svg="http://www.w3.org/2000/svg">

<!-- =========================================================== -->
<!-- This stylesheet exports a named template to draw barcodes   -->
<!-- using EAN-13, EAN-8, UPC-A, or UPC-E encoding scheme. The   --> 
<!-- stylesheet produces a barcode pattern as an SVG graphic.    -->
<!--                                                             -->
<!-- Template arguments have the following meaning:              -->
<!--                                                             -->
<!--    $module - a numeric value of a narrowest bar width       -->
<!--    $unit   - measurement unit for $module                   -->
<!--                                                             -->
<!-- Example: if the narrowest bar is 0.33mm,                    -->
<!--          then $unit="mm", $module="0.33"                    -->   
<!-- These two parameters serve to provide an easy scaling of    -->
<!-- barcode picture. All widths inside the template are         -->
<!-- measured in modules; to convert such a relative value to an -->
<!-- absolute length, it is necessary to multiply it by $module  -->
<!-- and concatenate the result of multiplication with $unit.    -->
<!--                                                             -->
<!--    $height - short bar width (measured in $modules)         -->
<!--                                                             -->
<!-- It is expected that guard bars will be longer than this.    -->
<!--                                                             -->
<!--    $bar-and-space-widths - a string of widths (in $modules) -->
<!--                                                             -->
<!-- This string of digits specifies a complete pattern for all  -->
<!-- bars and spaces in the barcode - including guard bars. Odd  -->
<!-- positions correspond to bars, even positions correspond to  -->
<!-- spaces between them. In UPC/EAN, bars and spaces can only   -->
<!-- be 1, 2, 3, or 4 modules wide.                              -->
<!--                                                             -->
<!--    $bar-heights - a string of '|' and '.' symbols.          -->
<!--                                                             -->
<!-- This string specifies the pattern of long and short bars.   -->
<!-- It has a character for every bar; if the character is '|',  -->
<!-- the bar should be longer than $height - it is a guard bar;  -->
<!-- if the character is '.', the bar is a regular one. This     -->
<!-- pattern depends on the EAN/UPC variant; for example, UPC-A  -->
<!-- has the pattern of "||||..........||..........||||".        -->  
<!--                                                             -->
<!--    $first-digit - digit before the leading guard bars       -->
<!--    $last-digit - digit after the trailing guard bars        -->
<!--    $left-digits - left group of bottom digits               -->
<!--    $right-digits - right group of bottom digits             -->
<!--                                                             -->
<!-- This string specifies digits to be written at the bottom.   -->
<!-- $left-digits is always present, others may be empty.        -->
<!--                                                             -->
<!--    $leading-guards-width                                    -->
<!--    $center-guards-width                                     -->
<!--    $trailing-guards-width                                   -->
<!--    $left-short-bars-width                                   -->
<!--    $right-short-bars-width                                  -->
<!--                                                             -->
<!-- These parameters specify widths of correspondent parts of   -->
<!-- the picture in $modules. The first three measure distance   -->
<!-- from the left edge of the leftmost long bar in the group to -->
<!-- the right edge of the rightmost long bar. The last two      -->
<!-- measure the distance between the guard bars that delimit    -->
<!-- the corresponding string, i.e. include the surrounding      -->
<!-- white space also. The sum of the five parameters is always  -->
<!-- equal to the total width of the bar pattern. If a group     -->
<!-- of bars is missing in a particular code, its width is 0.    -->
<!--                                                             -->
<!--    $short-bars-in-group                                     -->
<!--                                                             -->
<!-- Number of short bars in a single group, either right or     -->
<!-- left. This parameter is useful for table representations.   -->
<!-- =========================================================== -->

<xsl:template name="draw-barcode-EAN">
  <xsl:param name="module"/>
  <xsl:param name="unit"/>
  <xsl:param name="height"/>
  <xsl:param name="bar-and-space-widths"/>
  <xsl:param name="bar-heights"/>
  <xsl:param name="first-digit"/>
  <xsl:param name="last-digit"/>
  <xsl:param name="left-digits"/>
  <xsl:param name="right-digits"/>
  <xsl:param name="leading-guards-width"/> 
  <xsl:param name="trailing-guards-width"/>
  <xsl:param name="center-guards-width"/>  
  <xsl:param name="left-short-bars-width"/>
  <xsl:param name="right-short-bars-width"/>
  <xsl:param name="short-bars-in-group"/>  <!-- unused --> 
   <xsl:param name="barcode-type"/>
  <!-- Preliminary calculations -->
  
  <!-- Select font height (in modules) and family. -->
  <xsl:variable name="font-height" select="11"/>
  <xsl:variable name="font-family" select="'Helvetica'"/>

  <!-- Calculate some useful widths (in modules) -->
  <xsl:variable name="first-digit-width"
                select="$font-height * string-length($first-digit)"/>
  <xsl:variable name="last-digit-width"
                select="$font-height * string-length($last-digit)"/>
  <xsl:variable name="start-left-side-digits"
                select="$first-digit-width + $leading-guards-width"/>
  <xsl:variable name="start-right-side-digits"
                select="$start-left-side-digits + $left-short-bars-width + $center-guards-width"/>
  <xsl:variable name="start-last-digit"
                select="$start-right-side-digits + $right-short-bars-width + $trailing-guards-width"/>
  <xsl:variable name="total-width"
                select="$start-last-digit + $last-digit-width"/>

  <svg:svg width="{$total-width * $module}{$unit}"
           height="{($height + $font-height + 2) * $module}{$unit}">

<!--  	<desc xmlns:mydoc="http://example.org/mydoc">
  		<barcode value="{$first-digit}{$left-digits}{$right-digits}{$last-digit}" type="{$barcode-type}"></barcode>
  	</desc>-->
    <!-- Draw all bars -->
    <xsl:message><xsl:text>[INFO] Drawing barcode bars</xsl:text></xsl:message>
    <xsl:call-template name="draw-bars">
      <xsl:with-param name="bar-and-space-widths" select="$bar-and-space-widths"/>
      <xsl:with-param name="bar-heights" select="$bar-heights"/>
      <xsl:with-param name="short-bar" select="$height"/>
      <xsl:with-param name="long-bar" select="$height + ($font-height div 2)"/>
      <xsl:with-param name="module" select="$module"/>      
      <xsl:with-param name="unit" select="$unit"/>
      <xsl:with-param name="offset" select="$first-digit-width"/>
    </xsl:call-template>    


    <!-- Draw digits -->
    <xsl:message><xsl:text>[INFO] Drawing barcode digits</xsl:text></xsl:message>
    <!-- Common font style -->
    <xsl:variable name="font-style"
                  select="concat('font-size: ', $font-height * $module , $unit,
                                 '; font-family: ', $font-family,
                                 '; fill: black;')"/>
    <xsl:variable name="vertical-offset"
                  select="concat(($height + $font-height) * $module, $unit)"/>
    
    <xsl:if test="$first-digit">
      <svg:text x="{($first-digit-width - 1) * $module}{$unit}"
                y="{$vertical-offset}"
                style="{$font-style} text-anchor: end;">
        <xsl:value-of select="$first-digit"/>
      </svg:text>
    </xsl:if>

    <xsl:if test="$left-digits">
      <svg:text x="{($start-left-side-digits + ( $left-short-bars-width div 2 )) * $module}{$unit}"
                y="{$vertical-offset}"
                style="{$font-style} text-anchor: middle;">
        <xsl:value-of select="$left-digits"/>
      </svg:text>
    </xsl:if>

    <xsl:if test="$right-digits">
      <svg:text x="{($start-right-side-digits + ( $right-short-bars-width div 2 )) * $module}{$unit}"
                y="{$vertical-offset}"
                style="{$font-style} text-anchor: middle;">
        <xsl:value-of select="$right-digits"/>
      </svg:text>
    </xsl:if>

    <xsl:if test="$last-digit">
      <svg:text x="{($start-last-digit + 1) * $module}{$unit}"
                y="{$vertical-offset}"
                style="{$font-style} text-anchor: start;">
        <xsl:value-of select="$last-digit"/>
      </svg:text>
    </xsl:if>
  </svg:svg>
</xsl:template>

<!-- =========================================================== -->
<!-- Recursive template: draws bars until $bar-and-space-widths  -->
<!-- is not empty.                                               -->

<xsl:template name="draw-bars">
  <xsl:param name="bar-and-space-widths"/>
  <xsl:param name="bar-heights"/>
  <xsl:param name="short-bar"/>
  <xsl:param name="long-bar"/>
  <xsl:param name="module"/>      
  <xsl:param name="unit"/>
  <xsl:param name="offset"/>

  <xsl:variable name="bar-length">
    <xsl:choose>
      <xsl:when test="substring($bar-heights, 1, 1) = '|'">
        <xsl:value-of select="$long-bar"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$short-bar"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
      
  <!-- Draw a bar -->  
  <svg:rect y="0" height="{$bar-length * $module}{$unit}"
            x="{$offset * $module}{$unit}" 
            width="{substring($bar-and-space-widths, 1, 1) * $module}{$unit}" 
            style="fill: black;"/>

  <xsl:if test="substring($bar-and-space-widths, 3)">
    <xsl:call-template name="draw-bars">
      <xsl:with-param name="bar-and-space-widths" select="substring($bar-and-space-widths, 3)"/>
      <xsl:with-param name="bar-heights" select="substring($bar-heights, 2)"/>
      <xsl:with-param name="short-bar" select="$short-bar"/>
      <xsl:with-param name="long-bar" select="$long-bar"/>
      <xsl:with-param name="module" select="$module"/>      
      <xsl:with-param name="unit" select="$unit"/>
      <xsl:with-param name="offset" select="$offset
                                          + substring($bar-and-space-widths, 1, 1)
                                          + substring($bar-and-space-widths, 2, 1)"/>
    </xsl:call-template>        
  </xsl:if>

</xsl:template>    


<!-- =========================================================== -->
<!-- Emergency template: called when the input is incorrect      -->

<xsl:template name="draw-error-message">
<xsl:message><xsl:text>[BARCODE GENERATOR - SVG] incorrect input</xsl:text></xsl:message>
</xsl:template>

</xsl:stylesheet>
