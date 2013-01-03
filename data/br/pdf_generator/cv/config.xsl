<!--
    XSL Stylsheet for CV Book: Configuration file

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <!-- Some general parameters to play with -->
    <!-- Page size in millimeters -->
    <xsl:variable name="page-width"             select="210"/>
    <xsl:variable name="page-height"            select="297"/>

    <!-- Page margins in millimeters -->
    <xsl:variable name="margin-x"               select="20"/>
    <xsl:variable name="margin-y"               select="14"/>

    <!-- Derived variables -->
    <xsl:variable name="content-height"         select="$page-height - 2 * $margin-y"/>

    <!-- Margins for the sections -->
    <xsl:variable name="section-top"            select="2"/>
    <xsl:variable name="section-content-left"   select="2"/>
    <xsl:variable name="subsection-top"         select="1"/>

    <!-- The width of the profile picture -->
    <xsl:variable name="picture-width"          select="25"/>

    <!-- The font size -->
    <xsl:variable name="font-size"              select="10"/>
    <xsl:variable name="title-font-size"        select="30"/>
    <xsl:variable name="line-height"            select="1.4"/>
    <xsl:variable name="title-line-height"      select="2"/>
</xsl:stylesheet>