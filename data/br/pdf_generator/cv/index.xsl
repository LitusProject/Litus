<!--
    XSL Stylsheet for CV Book: the alphabetical index

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:import href="config.xsl"/>

    <!-- The table of contents -->
    <xsl:template name="index">
        <fo:block break-before="page">

            <!-- Set a marker: change the footer text -->
            <fo:marker marker-class-name="footer-text">
                Alfabetische Index
            </fo:marker>

            <xsl:for-each select="/cvbook/cvgroup/cv">

                <xsl:sort select="@lastname"/>
                <xsl:sort select="@firstname"/>

                <fo:block>
                    <xsl:value-of select="@firstname"/><xsl:text> </xsl:text><xsl:value-of select="@lastname"/> -

                    <fo:page-number-citation>
                        <xsl:attribute name="ref-id">
                            <xsl:value-of select="@id"/>
                        </xsl:attribute>
                    </fo:page-number-citation>

                </fo:block>
            </xsl:for-each>
        </fo:block>
    </xsl:template>

</xsl:stylesheet>
