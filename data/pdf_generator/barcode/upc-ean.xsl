<?xml version="1.0" encoding="iso-8859-1"?>

<!-- =========================================================== -->
<!--                                                             -->
<!-- (c) 2000 - 2003, RenderX                                    -->
<!--                                                             -->
<!-- Author: Nikolai Grigoriev <grig@renderx.com>                -->
<!--                                                             -->
<!-- Permission is granted to use this document, copy and        -->
<!-- modify free of charge, provided that every derived work     -->
<!-- bear a reference to the present document.                   -->
<!--                                                             -->
<!-- This document contains a computer program written in        -->
<!-- XSL Transformations Language. It is published with no       -->
<!-- warranty of any kind about its usability, as a mere         -->
<!-- example of XSL technology. RenderX shall not be considered  -->
<!-- liable for any damage or loss of data caused by use         -->
<!-- of this program.                                            -->
<!--                                                             -->
<!-- =========================================================== -->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- =========================================================== -->
<!-- This stylesheet contains a named template that encodes      -->
<!-- given data using EAN-13, EAN-8, UPC-A, or UPC-E barcode     -->
<!-- encoding scheme. It call named template (exported by        -->
<!-- upc-ean-svg.xsl) to draw SVG image of the EAN/UPC barcode.  -->
<!--                                                             -->
<!-- The template provides check digit calculation. It assumes   -->
<!-- the argument is exactly 6,7, 11 or 12 digits long.          -->
<!-- The barcodes produced are of the following type:            -->
<!--    6 digits - UPC E, number system 0 (zero is prepended)    -->
<!--    7 digits - UPC E or EAN 8                                -->
<!--   11 digits - UPC A                                         -->
<!--   12 digits - EAN 13                                        -->
<!-- You can override the default barcode treatment and set      -->
<!-- code-type parameter explicitly; this is required to         -->
<!-- distinguish between EAN 8 and UPC E codes when the latter   -->
<!-- starts with 1. UPC-E codes not starting with 0 or 1         -->
<!-- are rejected.                                               -->
<!--                                                             -->
<!--                    Mandatory parameters are:                -->
<!--                                                             -->
<!--  "value" -  a string of digits to encode. Digits may be     -->
<!--             separated by spaces, commas, dots, or dashes.   -->
<!--                                                             -->
<!--                     Optional parameters are:                -->
<!--                                                             -->
<!--  "code-type" - one of 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E'.  -->
<!--             Default depends on $value string length; for    -->
<!--             7-digit sequences, default is EAN-8.            -->
<!--  "module" - width of the elementary unit bar/space.         -->
<!--             Default is 0.33 mm (0.013 in).                  -->
<!--  "height" - pattern height (= bar length). Default is       -->
<!--             23 mm for EAN-13 and UPC-A, 18 mm for EAN-8,    -->
<!--             and 14 mm for UPC-E.                            -->
<!--                                                             -->
<!-- IMPORTANT: the $value parameter should not include the      -->
<!-- check digit (the last digit on the barcode patterns) -      -->
<!-- it will be generated automatically by the stylesheet.       -->
<!--                                                             -->
<!-- This topmost template performs length checks and calculates -->
<!-- default values for parameters, then calls the template      -->
<!-- "draw-barcode-EAN" from the imported stylesheet. If the     -->
<!-- length of the input string does not match the specified     -->
<!-- barcode type or the barcode type is invalid,                -->
<!-- "draw-error-message" template is called from the imported   -->
<!-- stylesheet. It is expected that this template generates     -->
<!-- a box with an error message. Moreover, diagnostic messages  -->
<!-- are sent to console via <xsl:message>.                      -->
<!-- =========================================================== -->

<xsl:import href="upc-ean-svg.xsl"/>

<xsl:template name="barcode-EAN">
  <xsl:param name="value"/>
  <xsl:param name="code-type" select="'auto'"/>
  <xsl:param name="module" select="'0.33mm'"/>
  <xsl:param name="height" select="'auto'"/>

  <!-- Remove separators -->
  <xsl:variable name="cleaned-value"
        select="translate ($value, ' &#x9;&#xA;-,.:;', '')"/>
  <!-- Get input length; used in checks -->
  <xsl:variable name="cleaned-value-length" select="string-length($cleaned-value)"/>

  <!-- Calculate the default code type, check data for consistency -->
  <xsl:variable name="real-code-type">
    <xsl:choose>
      <xsl:when test="$code-type != 'auto'">
        <xsl:choose>
          <xsl:when test="$code-type = 'UPC-A'  or $code-type = 'UPC-E'
                       or $code-type = 'EAN-13' or $code-type = 'EAN-8'">
            <xsl:value-of select="$code-type"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>ERROR</xsl:text>
            <xsl:message>
              <xsl:text>[BARCODE GENERATOR] Invalid barcode system: </xsl:text>
              <xsl:value-of select="$code-type"/>
              <xsl:text>&#xA;Should be one of "UPC-A", "UPC-E", "EAN-13", "EAN-8", or "auto"&#xA;</xsl:text>
            </xsl:message>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>

      <xsl:when test="$cleaned-value-length = 6">UPC-E</xsl:when>
      <xsl:when test="$cleaned-value-length = 7">EAN-8</xsl:when>
      <xsl:when test="$cleaned-value-length = 11">UPC-A</xsl:when>
      <xsl:when test="$cleaned-value-length = 12">EAN-13</xsl:when>
      <xsl:otherwise>
        <xsl:text>ERROR</xsl:text>
        <xsl:message>
          <xsl:text>[BARCODE GENERATOR] Incorrect length of input sequence: </xsl:text>
          <xsl:value-of select="$cleaned-value"/>
          <xsl:text>&#xA;Should be exactly 6, 7, 11, or 12 digits.&#xA;</xsl:text>
        </xsl:message>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

  <!-- Check the input; add a leading zero to 6-digit UPC-E codes -->
  <xsl:variable name="real-value">
    <xsl:choose>
      <xsl:when test="$real-code-type = 'UPC-E' and $cleaned-value-length = 6">
        <xsl:value-of select="concat('0', $cleaned-value)"/>
      </xsl:when>
      <xsl:when test="$cleaned-value-length = 6  or $cleaned-value-length = 7
                   or $cleaned-value-length = 11 or $cleaned-value-length = 12">
        <xsl:value-of select="$cleaned-value"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>ERROR</xsl:text>
        <xsl:message>
          <xsl:text>[BARCODE GENERATOR] Incorrect length of input sequence: </xsl:text>
          <xsl:value-of select="$cleaned-value"/>
          <xsl:text>&#xA;Should be exactly 6, 7, 11, or 12 digits.&#xA;</xsl:text>
        </xsl:message>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

  <!-- Parse module width specifier -->
  <xsl:variable name="module-numeric-value" select="translate ($module, 'ptxcinme ', '')"/>
  <xsl:variable name="module-unit" select="translate ($module, '-0123456789. ', '')"/>

  <!-- Calculate the default height for various code types. -->
  <!-- Height is expressed in module units.                 -->
  <xsl:variable name="real-height">
    <xsl:choose>
      <xsl:when test="$height != 'auto'">
        <xsl:call-template name="convert-height-to-module-units">
          <xsl:with-param name="module-numeric-value" select="$module-numeric-value"/>
          <xsl:with-param name="module-unit" select="$module-unit"/>
          <xsl:with-param name="height" select="$height"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:when test="$real-code-type = 'UPC-E'">42</xsl:when>
      <xsl:when test="$real-code-type = 'EAN-8'">56</xsl:when>
      <xsl:otherwise>70</xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

  <!-- Check data for consistency and invoke the processing template -->
  <xsl:choose>
    <xsl:when test="(string-length($real-value) = 7 and $real-code-type = 'EAN-8')
                 or (string-length($real-value) = 11 and $real-code-type = 'UPC-A')
                 or (string-length($real-value) = 12 and $real-code-type = 'EAN-13')
                 or (string-length($real-value) = 7 and $real-code-type = 'UPC-E'
                    and (substring($real-value, 1, 1) = '0'
                      or substring($real-value, 1, 1) = '1'))">

      <!-- Calculate the check digit -->
      <xsl:variable name="check-digit">
        <xsl:call-template name="EAN-check-digit">
          <xsl:with-param name="value" select="$real-value"/>
          <xsl:with-param name="code-type" select="$real-code-type"/>
        </xsl:call-template>
      </xsl:variable>

      <xsl:call-template name="barcode-EAN-cleaned">
        <xsl:with-param name="value" select="concat($real-value, $check-digit)"/>
        <xsl:with-param name="unit" select="$module-unit"/>
        <xsl:with-param name="module" select="$module-numeric-value"/>
        <xsl:with-param name="height" select="$real-height"/>
        <xsl:with-param name="code-type" select="$real-code-type"/>
      </xsl:call-template>

    </xsl:when>
    <xsl:otherwise>
      <xsl:message>
        <xsl:text>[BARCODE GENERATOR] Input sequence </xsl:text>
        <xsl:value-of select="$cleaned-value"/>
        <xsl:text>does not match the requested code type </xsl:text>
        <xsl:value-of select="$code-type"/>
        <xsl:text>&#xA;</xsl:text>
      </xsl:message>
      <!-- call error handler - specific for the output format -->
      <xsl:call-template name="draw-error-message"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- ========================================================= -->
<!-- This template has the same parameters as the one before;  -->
<!-- it gets the processed arguments and does not perform any  -->
<!-- further error checking. It parses and prepares all data,  -->
<!-- splitting them into a dozen of variables, and passes      -->
<!-- control to the specific drawing template.                 -->

<xsl:template name="barcode-EAN-cleaned">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>
  <xsl:param name="module"/>
  <xsl:param name="height"/>
  <xsl:param name="unit"/>

  <!-- Prepare string components -->

  <!-- Get the first digit -->
  <xsl:variable name="first-digit">
    <xsl:call-template name="get-first-digit">
      <xsl:with-param name="value" select="$value"/>
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the last digit -->
  <xsl:variable name="last-digit">
    <xsl:call-template name="get-last-digit">
      <xsl:with-param name="value" select="$value"/>
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the left-side string -->
  <xsl:variable name="left-digits">
    <xsl:call-template name="get-left-side-digits">
      <xsl:with-param name="value" select="$value"/>
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the right-side string -->
  <xsl:variable name="right-digits">
    <xsl:call-template name="get-right-side-digits">
      <xsl:with-param name="value" select="$value"/>
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the bar/space width pattern -->
  <xsl:variable name="bar-and-space-widths">
    <xsl:call-template name="get-bar-space-width">
      <xsl:with-param name="value" select="$value"/>
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the bar height pattern -->
  <xsl:variable name="bar-heights">
    <xsl:call-template name="get-bar-height">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the width of leading guard bars, in modules -->
  <xsl:variable name="leading-guards-width">
    <xsl:call-template name="get-leading-guards-width">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the width of trailing guard bars, in modules -->
  <xsl:variable name="trailing-guards-width">
    <xsl:call-template name="get-trailing-guards-width">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the width of center guard bars, in modules -->
  <xsl:variable name="center-guards-width">
    <xsl:call-template name="get-center-guards-width">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the width of the left group of short bars, in modules -->
  <xsl:variable name="left-short-bars-width">
    <xsl:call-template name="get-left-short-bars-width">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the width of the right group of short bars, in modules -->
  <xsl:variable name="right-short-bars-width">
    <xsl:call-template name="get-right-short-bars-width">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Get the number of short bars on either right or left side -->
  <xsl:variable name="short-bars-in-group">
    <xsl:call-template name="count-short-bars-in-group">
      <xsl:with-param name="code-type" select="$code-type"/>
    </xsl:call-template>
  </xsl:variable>

  <!-- Call an imported template, passing parsed data to it -->
  <xsl:call-template name="draw-barcode-EAN">

    <!-- Dimensional attributes -->
    <xsl:with-param name="module" select="$module"/>
    <xsl:with-param name="unit" select="$unit"/>
    <xsl:with-param name="height" select="$height"/>

    <!-- String components -->
    <xsl:with-param name="first-digit" select="normalize-space($first-digit)"/>
    <xsl:with-param name="last-digit" select="normalize-space($last-digit)"/>
    <xsl:with-param name="left-digits" select="normalize-space($left-digits)"/>
    <xsl:with-param name="right-digits" select="normalize-space($right-digits)"/>

    <!-- Bar/space width pattern -->
    <xsl:with-param name="bar-and-space-widths" select="$bar-and-space-widths"/>

    <!-- Bar height pattern -->
    <xsl:with-param name="bar-heights" select="$bar-heights"/>

    <!-- Width of leading guard bars, in modules -->
    <xsl:with-param name="leading-guards-width" select="$leading-guards-width"/>

    <!-- Width of trailing guard bars, in modules -->
    <xsl:with-param name="trailing-guards-width" select="$trailing-guards-width"/>

    <!-- Width of center guard bars, in modules -->
    <xsl:with-param name="center-guards-width" select="$center-guards-width"/>

    <!-- Width of the left group of short bars, in modules -->
    <xsl:with-param name="left-short-bars-width" select="$left-short-bars-width"/>

    <!-- Width of the right group of short bars, in modules -->
    <xsl:with-param name="right-short-bars-width" select="$right-short-bars-width"/>

    <!-- Number of short bars on either right or left side -->
    <xsl:with-param name="short-bars-in-group" select="$short-bars-in-group"/>

     <xsl:with-param name="barcode-type" select="$code-type"/>
  	
  </xsl:call-template>
</xsl:template>


<!-- =========================================================== -->
<!-- Calculate EAN/UPC checksum. Note that "odds" and "evens"    -->
<!-- are counted from the end of the sequence.                   -->

<xsl:template name="EAN-check-digit">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <!-- Expand UPC-E code to full 11-digit UPC-A -->
  <xsl:variable name="real-value">
    <xsl:choose>
      <xsl:when test="$code-type = 'UPC-E'">
        <xsl:variable name="last-digit" select="substring($value, 7, 1)"/>
        <xsl:choose>
          <xsl:when test="$last-digit='0' or $last-digit='1' or $last-digit='2'">
            <xsl:value-of select="concat(substring($value, 1, 3),
                                         $last-digit, '0000',
                                         substring($value, 4, 3))"/>
          </xsl:when>
          <xsl:when test="$last-digit='3'">
            <xsl:value-of select="concat(substring($value, 1, 4),
                                         '00000',
                                         substring($value, 5, 2))"/>
          </xsl:when>
          <xsl:when test="$last-digit='4'">
            <xsl:value-of select="concat(substring($value, 1, 5),
                                         '00000',
                                         substring($value, 6, 1))"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="concat(substring($value, 1, 6),
                                         '0000',
                                         substring($value, 7, 1))"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise><xsl:value-of select="$value"/></xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

  <xsl:variable name="sum-odds">
    <xsl:choose>
      <xsl:when test="$code-type = 'EAN-13'">
        <xsl:variable name="d2"  select="number(substring($real-value, 2, 1))"/>
        <xsl:variable name="d4"  select="number(substring($real-value, 4, 1))"/>
        <xsl:variable name="d6"  select="number(substring($real-value, 6, 1))"/>
        <xsl:variable name="d8"  select="number(substring($real-value, 8, 1))"/>
        <xsl:variable name="d10" select="number(substring($real-value, 10, 1))"/>
        <xsl:variable name="d12" select="number(substring($real-value, 12, 1))"/>
        <xsl:value-of select="$d2 + $d4 + $d6 + $d8 + $d10 + $d12" />
      </xsl:when>
      <xsl:when test="$code-type = 'UPC-A' or $code-type = 'UPC-E'">
        <xsl:variable name="d1"  select="number(substring($real-value, 1, 1))"/>
        <xsl:variable name="d3"  select="number(substring($real-value, 3, 1))"/>
        <xsl:variable name="d5"  select="number(substring($real-value, 5, 1))"/>
        <xsl:variable name="d7"  select="number(substring($real-value, 7, 1))"/>
        <xsl:variable name="d9"  select="number(substring($real-value, 9, 1))"/>
        <xsl:variable name="d11" select="number(substring($real-value, 11, 1))"/>
        <xsl:value-of select="$d1 + $d3 + $d5 + $d7 + $d9 + $d11" />
      </xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">
        <xsl:variable name="d1"  select="number(substring($real-value, 1, 1))"/>
        <xsl:variable name="d3"  select="number(substring($real-value, 3, 1))"/>
        <xsl:variable name="d5"  select="number(substring($real-value, 5, 1))"/>
        <xsl:variable name="d7"  select="number(substring($real-value, 7, 1))"/>
        <xsl:value-of select="$d1 + $d3 + $d5 + $d7" />
      </xsl:when>
    </xsl:choose>
  </xsl:variable>

  <xsl:variable name="sum-evens">
    <xsl:choose>
      <xsl:when test="$code-type = 'EAN-13'">
        <xsl:variable name="d1"  select="number(substring($real-value, 1, 1))"/>
        <xsl:variable name="d3"  select="number(substring($real-value, 3, 1))"/>
        <xsl:variable name="d5"  select="number(substring($real-value, 5, 1))"/>
        <xsl:variable name="d7"  select="number(substring($real-value, 7, 1))"/>
        <xsl:variable name="d9"  select="number(substring($real-value, 9, 1))"/>
        <xsl:variable name="d11" select="number(substring($real-value, 11, 1))"/>
        <xsl:value-of select="$d1 + $d3 + $d5 + $d7 + $d9 + $d11" />
      </xsl:when>
      <xsl:when test="$code-type = 'UPC-A' or $code-type = 'UPC-E'">
        <xsl:variable name="d2"  select="number(substring($real-value, 2, 1))"/>
        <xsl:variable name="d4"  select="number(substring($real-value, 4, 1))"/>
        <xsl:variable name="d6"  select="number(substring($real-value, 6, 1))"/>
        <xsl:variable name="d8"  select="number(substring($real-value, 8, 1))"/>
        <xsl:variable name="d10" select="number(substring($real-value, 10, 1))"/>
        <xsl:value-of select="$d2 + $d4 + $d6 + $d8 + $d10" />
      </xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">
        <xsl:variable name="d2"  select="number(substring($real-value, 2, 1))"/>
        <xsl:variable name="d4"  select="number(substring($real-value, 4, 1))"/>
        <xsl:variable name="d6"  select="number(substring($real-value, 6, 1))"/>
        <xsl:value-of select="$d2 + $d4 + $d6" />
      </xsl:when>
    </xsl:choose>
  </xsl:variable>

  <!-- Multiply $sum-odds by 3 and add to $sum-evens -->
  <xsl:variable name="total-sum"
                select="($sum-odds * 3) + $sum-evens" />

  <!-- Get complement of $total-sum to the nearest multiple of 10 -->
  <xsl:value-of select="(10 - ($total-sum mod 10)) mod 10"/>
</xsl:template>

<!-- =========================================================== -->
<!-- This template produces the barcode pattern as a sequence of -->
<!-- digits from 1 to 4 that correspond to widths of bars and    -->
<!-- spaces between them, in module units                        -->

<xsl:template name="get-bar-space-width">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <!-- Get the left-side pattern. UPC-A and EAN-8 use no hidden digits; -->
  <!-- their patterns will be sequences of A's. For EAN-13, the pattern -->
  <!-- is selected by the first digit; for UPC-8, by the combination    -->
  <!-- of the first digit (number system) and the last digit (checksum) -->
  <xsl:variable name="left-pattern">
    <xsl:choose>
      <xsl:when test="$code-type = 'UPC-A'">AAAAAA</xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">AAAA</xsl:when>

      <xsl:when test="$code-type = 'EAN-13'">
        <xsl:call-template name="get-pattern-EAN-13">
          <xsl:with-param name="switch" select="substring($value,1,1)"/>
        </xsl:call-template>
      </xsl:when>

      <xsl:when test="$code-type = 'UPC-E' and substring($value, 1, 1) = '0'">
        <xsl:call-template name="get-pattern-UPC-E0">
          <xsl:with-param name="switch" select="substring($value,string-length($value),1)"/>
        </xsl:call-template>
      </xsl:when>

      <xsl:when test="$code-type = 'UPC-E' and substring($value, 1, 1) = '1'">
        <xsl:call-template name="get-pattern-UPC-E1">
          <xsl:with-param name="switch" select="substring($value,string-length($value),1)"/>
        </xsl:call-template>
      </xsl:when>
    </xsl:choose>
  </xsl:variable>

  <!-- Get the right-side pattern. UPC-E has no right side. -->
  <xsl:variable name="right-pattern">
    <xsl:choose>
      <xsl:when test="$code-type = 'EAN-13'">AAAAAA</xsl:when>
      <xsl:when test="$code-type = 'UPC-A'">AAAAAA</xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">AAAA</xsl:when>
    </xsl:choose>
  </xsl:variable>

  <!-- Get the left-side string -->
  <xsl:variable name="left-value">
    <xsl:choose>
      <xsl:when test="$code-type = 'EAN-13' or $code-type = 'UPC-E'">
        <xsl:value-of select="substring($value, 2, 6)"/>
      </xsl:when>
      <xsl:when test="$code-type = 'UPC-A'">
        <xsl:value-of select="substring($value, 1, 6)"/>
      </xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">
        <xsl:value-of select="substring($value, 1, 4)"/>
      </xsl:when>
    </xsl:choose>
  </xsl:variable>

  <!-- Get the right-side string. UPC-E has no right side. -->
  <xsl:variable name="right-value">
    <xsl:choose>
      <xsl:when test="$code-type = 'EAN-13'">
        <xsl:value-of select="substring($value, 8, 6)"/>
      </xsl:when>
      <xsl:when test="$code-type = 'UPC-A'">
        <xsl:value-of select="substring($value, 7, 6)"/>
      </xsl:when>
      <xsl:when test="$code-type = 'EAN-8'">
        <xsl:value-of select="substring($value, 5, 4)"/>
      </xsl:when>
    </xsl:choose>
  </xsl:variable>

  <!-- Start emitting widths -->
  <!-- Leading guides -->
  <xsl:text>111</xsl:text>

  <!-- Encoding the left side characters -->
  <xsl:call-template name="emit-EAN-character-patterns">
    <xsl:with-param name="value" select="$left-value"/>
    <xsl:with-param name="pattern" select="$left-pattern"/>
  </xsl:call-template>

  <xsl:choose>
    <!-- For UPC-E, we should only add trailing bars -->
    <xsl:when test="$code-type = 'UPC-E'">
      <xsl:text>111111</xsl:text>
    </xsl:when>
    <xsl:otherwise>
      <!-- Center guides -->
      <xsl:text>11111</xsl:text>
      <!-- Encoding the right-side characters -->
      <xsl:call-template name="emit-EAN-character-patterns">
        <xsl:with-param name="value" select="$right-value"/>
        <xsl:with-param name="pattern" select="$right-pattern"/>
      </xsl:call-template>

      <!-- Trailing guides -->
      <xsl:text>111</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the alternation of long and short bars  -->
<!-- for each barcode type. Note that this refers only to bars,  -->
<!-- spaces are omitted. '|' - tall bar, '.' - low bar.          -->

<xsl:template name="get-bar-height">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='EAN-13'">||............||............||</xsl:when>
    <xsl:when test="$code-type='UPC-A'">||||..........||..........||||</xsl:when>
    <xsl:when test="$code-type='EAN-8'">||........||........||</xsl:when>
    <xsl:when test="$code-type='UPC-E'">||............|||</xsl:when>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the width of the leading guard bars,    -->
<!-- in module units. Note that this width does not include      -->
<!-- any surrounding spaces, extending from the first long bar   -->
<!-- in the group to the last one.                               -->

<xsl:template name="get-leading-guards-width">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-A'">10</xsl:when>
    <xsl:otherwise>3</xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the width of the trailing guard bars,   -->
<!-- in module units. Note that this width does not include      -->
<!-- any surrounding spaces, extending from the first long bar   -->
<!-- in the group to the last one.                               -->

<xsl:template name="get-trailing-guards-width">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-A'">10</xsl:when>
    <xsl:when test="$code-type='UPC-E'">5</xsl:when>
    <xsl:otherwise>3</xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the width of the center guard bars,     -->
<!-- in module units. Note that this width does not include      -->
<!-- any surrounding spaces, extending from the first long bar   -->
<!-- in the group to the last one.                               -->

<xsl:template name="get-center-guards-width">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-E'">0</xsl:when>
    <xsl:otherwise>3</xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the width of the left short bar group,  -->
<!-- in module units. This width includes all adjacent spaces.   -->

<xsl:template name="get-left-short-bars-width">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-A'">36</xsl:when>
    <xsl:when test="$code-type='EAN-8'">29</xsl:when>
    <xsl:otherwise>43</xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template gives the width of the right short bar group, -->
<!-- in module units. This width includes all adjacent spaces.   -->

<xsl:template name="get-right-short-bars-width">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-E'">0</xsl:when>
    <xsl:when test="$code-type='UPC-A'">36</xsl:when>
    <xsl:when test="$code-type='EAN-8'">29</xsl:when>
    <xsl:otherwise>43</xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- =========================================================== -->
<!-- This template gives the number of short bars in a group.    -->

<xsl:template name="count-short-bars-in-group">
  <xsl:param name="code-type"/>
  <xsl:choose>
    <xsl:when test="$code-type='UPC-A'">10</xsl:when>
    <xsl:when test="$code-type='EAN-8'">8</xsl:when>
    <xsl:otherwise>12</xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- =========================================================== -->
<!-- This template returns the first character if it should be   -->
<!-- drawn outside the bars (for all codes but EAN-8)            -->

<xsl:template name="get-first-digit">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <xsl:if test="$code-type != 'EAN-8'">
    <xsl:value-of select="substring($value, 1, 1)"/>
  </xsl:if>
</xsl:template>

<!-- =========================================================== -->
<!-- This template returns the last character if it should be    -->
<!-- drawn outside the bars (for UPC codes but not for EAN)      -->

<xsl:template name="get-last-digit">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <xsl:if test="$code-type = 'UPC-A' or $code-type = 'UPC-E'">
    <xsl:value-of select="substring($value, string-length($value), 1)"/>
  </xsl:if>
</xsl:template>

<!-- =========================================================== -->
<!-- This template returns the left-side string for display.     -->
<!-- Note that this is not the same as for getting bar widths:   -->
<!-- for UPC-A, the first digit is written outside the bars.     -->

<xsl:template name="get-left-side-digits">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <xsl:choose>
    <xsl:when test="$code-type = 'EAN-13' or $code-type = 'UPC-E'">
      <xsl:value-of select="substring($value, 2, 6)"/>
    </xsl:when>
    <xsl:when test="$code-type = 'UPC-A'">
      <xsl:value-of select="substring($value, 2, 5)"/>
    </xsl:when>
    <xsl:when test="$code-type = 'EAN-8'">
      <xsl:value-of select="substring($value, 1, 4)"/>
    </xsl:when>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- This template returns the right-side string for display.    -->
<!-- Note that this is not the same as for getting bar widths:   -->
<!-- for UPC-A, the last digit is written outside the bars.      -->
<!-- For UPC-E, the template returns an empty string.            -->

<xsl:template name="get-right-side-digits">
  <xsl:param name="value"/>
  <xsl:param name="code-type"/>

  <xsl:choose>
    <xsl:when test="$code-type = 'EAN-13'">
      <xsl:value-of select="substring($value, 8, 6)"/>
    </xsl:when>
    <xsl:when test="$code-type = 'UPC-A'">
      <xsl:value-of select="substring($value, 7, 5)"/>
    </xsl:when>
    <xsl:when test="$code-type = 'EAN-8'">
      <xsl:value-of select="substring($value, 5, 4)"/>
    </xsl:when>
  </xsl:choose>
</xsl:template>


<!-- =========================================================== -->
<!-- Get hidden digit pattern for EAN-13                         -->

<xsl:template name="get-pattern-EAN-13">
  <xsl:param name="switch"/>
  <xsl:choose>
    <xsl:when test="$switch='0'">AAAAAA</xsl:when>
    <xsl:when test="$switch='1'">AABABB</xsl:when>
    <xsl:when test="$switch='2'">AABBAB</xsl:when>
    <xsl:when test="$switch='3'">AABBBA</xsl:when>
    <xsl:when test="$switch='4'">ABAABB</xsl:when>
    <xsl:when test="$switch='5'">ABBAAB</xsl:when>
    <xsl:when test="$switch='6'">ABBBAA</xsl:when>
    <xsl:when test="$switch='7'">ABABAB</xsl:when>
    <xsl:when test="$switch='8'">ABABBA</xsl:when>
    <xsl:when test="$switch='9'">ABBABA</xsl:when>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- Get hidden digit pattern for UPC-E, number system 0         -->

<xsl:template name="get-pattern-UPC-E0">
  <xsl:param name="switch"/>
  <xsl:choose>
    <xsl:when test="$switch='0'">BBBAAA</xsl:when>
    <xsl:when test="$switch='1'">BBABAA</xsl:when>
    <xsl:when test="$switch='2'">BBAABA</xsl:when>
    <xsl:when test="$switch='3'">BBAAAB</xsl:when>
    <xsl:when test="$switch='4'">BABBAA</xsl:when>
    <xsl:when test="$switch='5'">BAABBA</xsl:when>
    <xsl:when test="$switch='6'">BAAABB</xsl:when>
    <xsl:when test="$switch='7'">BABABA</xsl:when>
    <xsl:when test="$switch='8'">BABAAB</xsl:when>
    <xsl:when test="$switch='9'">BAABAB</xsl:when>
  </xsl:choose>
</xsl:template>

<!-- =========================================================== -->
<!-- Get hidden digit pattern for UPC-E, number system 1         -->

<xsl:template name="get-pattern-UPC-E1">
  <xsl:param name="switch"/>
  <xsl:choose>
    <xsl:when test="$switch='0'">AAABBB</xsl:when>
    <xsl:when test="$switch='1'">AABABB</xsl:when>
    <xsl:when test="$switch='2'">AABBAB</xsl:when>
    <xsl:when test="$switch='3'">AABBBA</xsl:when>
    <xsl:when test="$switch='4'">ABAABB</xsl:when>
    <xsl:when test="$switch='5'">ABBAAB</xsl:when>
    <xsl:when test="$switch='6'">ABBBAA</xsl:when>
    <xsl:when test="$switch='7'">ABABAB</xsl:when>
    <xsl:when test="$switch='8'">ABABBA</xsl:when>
    <xsl:when test="$switch='9'">ABBABA</xsl:when>
  </xsl:choose>
</xsl:template>

<!-- ========================================================= -->
<!-- Produce patterns for one digit in EAN, then recurse.      -->

<xsl:template name="emit-EAN-character-patterns">
  <xsl:param name="value"/>
  <xsl:param name="pattern"/>

  <xsl:if test="string-length($pattern) &gt; 0">

    <xsl:call-template name="process-single-digit-EAN">
      <xsl:with-param name="digit" select="substring($value, 1,1)"/>
      <xsl:with-param name="pattern-type" select="substring($pattern, 1,1)"/>
    </xsl:call-template>

    <xsl:call-template name="emit-EAN-character-patterns">
      <xsl:with-param name="value" select="substring($value, 2)"/>
      <xsl:with-param name="pattern" select="substring($pattern, 2)"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


<!-- ========================================================= -->
<!-- Print out a specific pattern for EAN: calls one of two    -->
<!-- subtemplates according to the value of $pattern-type      -->

<xsl:template name="process-single-digit-EAN">
  <xsl:param name="digit"/>
  <xsl:param name="pattern-type"/>

  <xsl:choose>
    <xsl:when test="$pattern-type = 'B'">
      <xsl:call-template name="EAN-digit-B">
        <xsl:with-param name="digit" select="$digit"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:call-template name="EAN-digit-A">
        <xsl:with-param name="digit" select="$digit"/>
      </xsl:call-template>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- ========================================================= -->
<!-- Print out a specific pattern for EAN/UPC.                 -->
<!-- Split to 2 separate templates for readability.            -->
<!-- Note that non-digit input produces zero pattern. It is    -->
<!-- safer to produce zero pattern than to generate nothing:   -->
<!-- the templates should always generate exactly 4 digits.    -->


<!-- Type A -->

<xsl:template name="EAN-digit-A">
  <xsl:param name="digit"/>

  <xsl:choose>
    <xsl:when test="$digit='0'">3211</xsl:when>
    <xsl:when test="$digit='1'">2221</xsl:when>
    <xsl:when test="$digit='2'">2122</xsl:when>
    <xsl:when test="$digit='3'">1411</xsl:when>
    <xsl:when test="$digit='4'">1132</xsl:when>
    <xsl:when test="$digit='5'">1231</xsl:when>
    <xsl:when test="$digit='6'">1114</xsl:when>
    <xsl:when test="$digit='7'">1312</xsl:when>
    <xsl:when test="$digit='8'">1213</xsl:when>
    <xsl:when test="$digit='9'">3112</xsl:when>
    <xsl:otherwise>
      <xsl:message>
        <xsl:text>[BARCODE GENERATOR] Non-digit symbol &apos;</xsl:text>
        <xsl:value-of select="$digit"/>
        <xsl:text>&apos; replaced by &apos;0&apos;</xsl:text>
      </xsl:message>
      <xsl:text>3211</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- Type B is the reverse of type A; used in EAN 13 and UPC E -->

<xsl:template name="EAN-digit-B">
  <xsl:param name="digit"/>

  <xsl:choose>
    <xsl:when test="$digit='0'">1123</xsl:when>
    <xsl:when test="$digit='1'">1222</xsl:when>
    <xsl:when test="$digit='2'">2212</xsl:when>
    <xsl:when test="$digit='3'">1141</xsl:when>
    <xsl:when test="$digit='4'">2311</xsl:when>
    <xsl:when test="$digit='5'">1321</xsl:when>
    <xsl:when test="$digit='6'">4111</xsl:when>
    <xsl:when test="$digit='7'">2131</xsl:when>
    <xsl:when test="$digit='8'">3121</xsl:when>
    <xsl:when test="$digit='9'">2113</xsl:when>
    <xsl:otherwise>
      <xsl:message>
        <xsl:text>[BARCODE GENERATOR] Non-digit symbol &apos;</xsl:text>
        <xsl:value-of select="$digit"/>
        <xsl:text>&apos; replaced by &apos;0&apos;</xsl:text>
      </xsl:message>
      <xsl:text>1123</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- ========================================================= -->
<!-- Convert height to the same units as module.               -->

<xsl:template name="convert-height-to-module-units">
  <xsl:param name="module-numeric-value"/>
  <xsl:param name="module-unit"/>
  <xsl:param name="height"/>

  <xsl:variable name="height-numeric-value" select="translate ($height, 'ptxcinme ', '')"/>
  <xsl:variable name="height-unit" select="translate ($height, '-0123456789. ', '')"/>

  <xsl:choose>
    <!-- If the units are the same, we just copy the numeric value -->
    <!-- without performing further controls.                      -->
    <xsl:when test="$module-unit = $height-unit">
      <xsl:value-of select="$height-numeric-value div $module-numeric-value"/>
    </xsl:when>

    <xsl:otherwise>
      <xsl:variable name="module-scale-factor">
        <xsl:call-template name="get-unit-scaling-factor">
          <xsl:with-param name="unit" select="$module-unit"/>
        </xsl:call-template>
      </xsl:variable>

      <xsl:variable name="height-scale-factor">
        <xsl:call-template name="get-unit-scaling-factor">
          <xsl:with-param name="unit" select="$height-unit"/>
        </xsl:call-template>
      </xsl:variable>

      <xsl:value-of select=" ($height-numeric-value * $height-scale-factor) div
                             ($module-numeric-value * $module-scale-factor)"/>

    </xsl:otherwise>
  </xsl:choose>
</xsl:template>


<!-- ========================================================= -->
<!-- This template expresses all length units in 1/360s of mm. -->
<!-- This is the largest unit in which both 1pt and 1 mm get   -->
<!-- integer values. Also spellchecks length units.            -->

<xsl:template name="get-unit-scaling-factor">
  <xsl:param name="unit"/>

  <xsl:choose>
    <xsl:when test="$unit = 'cm'">3600</xsl:when>
    <xsl:when test="$unit = 'mm'">360</xsl:when>
    <xsl:when test="$unit = 'in'">9144</xsl:when>
    <xsl:when test="$unit = 'pt'">127</xsl:when>
    <xsl:when test="$unit = 'pc'">1524</xsl:when>
    <xsl:when test="$unit = 'em'">
      <xsl:text>1524</xsl:text> <!-- defaulting to 12pt -->
      <xsl:message>
        [BARCODE GENERATOR] Units of 'em' should not be mixed to other units;
        assuming 1 em = 1 pica.
      </xsl:message>
    </xsl:when>
    <xsl:when test="$unit = 'ex'">
      <xsl:text>700</xsl:text>  <!-- defaulting to 12pt x 0.46 -->
      <xsl:message>
        [BARCODE GENERATOR] Units of 'ex' should not be mixed to other units;
        assuming 1 ex = 0.46 pica.
      </xsl:message>
    </xsl:when>
    <xsl:otherwise>
      <xsl:text>360</xsl:text>  <!-- defaulting to 1mm -->
      <xsl:message>
        [BARCODE GENERATOR] Unknown unit '<xsl:value-of select="$unit"/>' should not be mixed to other units;
        assuming 1 <xsl:value-of select="$unit"/> = 1 mm.
      </xsl:message>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>
</xsl:stylesheet>

