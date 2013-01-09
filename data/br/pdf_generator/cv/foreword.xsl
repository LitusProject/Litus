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
    <xsl:template name="foreword">
        <fo:block break-before="page">

            <!-- Set a marker: change the footer text -->
            <fo:marker marker-class-name="footer-text">
                <xsl:value-of select="/cvbook/@fw"/>
            </fo:marker>

            <!-- Title -->
            <fo:block
                line-height="{$title-line-height}"
                font-size="{$title-font-size}pt">

                <xsl:value-of select="/cvbook/@fw"/>

            </fo:block>

            <!-- TODO: foreword -->

        </fo:block>
    </xsl:template>

</xsl:stylesheet>
