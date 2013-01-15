<!--
    XSL Stylsheet for CV Book: the table of contents

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:import href="config.xsl"/>

    <!-- The table of contents -->
    <xsl:template match="foreword">
        <fo:block break-before="page">

            <!-- Set a marker: change the footer text -->
            <fo:marker marker-class-name="footer-text">
                <xsl:value-of select="@title"/>
            </fo:marker>

            <!-- Title -->
            <fo:block
                line-height="{$title-line-height}"
                font-size="{$title-font-size}pt">

                <xsl:value-of select="@title"/>

            </fo:block>

            <!-- Content -->
            <xsl:apply-templates select="section">
                <xsl:with-param name="size-factor" select="$font-size-decrease"/>
            </xsl:apply-templates>

        </fo:block>
    </xsl:template>

    <xsl:template match="section">
        <xsl:param name="size-factor"/>

        <!-- Title -->
        <fo:block
            line-height="{$title-line-height}">

            <!-- Set the font size to the title font size divided by the size-factor parameter -->
            <xsl:attribute name="font-size">
                <xsl:value-of select="concat(round($title-font-size div $size-factor), 'pt')"/>
            </xsl:attribute>

            <xsl:value-of select="@title"/>

        </fo:block>

        <fo:block>
            <xsl:value-of select="content"/>
        </fo:block>

        <xsl:apply-templates select="section">
            <xsl:with-param name="size-factor" select="$size-factor * $font-size-decrease"/>
        </xsl:apply-templates>

    </xsl:template>

</xsl:stylesheet>
