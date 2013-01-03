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
    <xsl:template name="toc">
        <fo:block break-before="page">

            <!-- Set a marker: change the footer text -->
            <fo:marker marker-class-name="footer-text">
                Inhoudstafel
            </fo:marker>

            <xsl:for-each select="/cvbook/cvgroup">
                <fo:block>
                    <xsl:value-of select="@name"/> -

                    <fo:page-number-citation>
                        <xsl:attribute name="ref-id">
                            <xsl:value-of select="@name"/>
                        </xsl:attribute>
                    </fo:page-number-citation>

                </fo:block>
            </xsl:for-each>
        </fo:block>
    </xsl:template>

</xsl:stylesheet>
