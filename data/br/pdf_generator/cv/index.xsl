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
        <fo:block break-before="odd-page">

            <!-- Set a marker: change the footer text -->
            <fo:marker marker-class-name="footer-text">
                <xsl:value-of select="/cvbook/@index"/>
            </fo:marker>

            <!-- Title -->
            <fo:block
                line-height="{$title-line-height}"
                font-size="{$title-font-size}pt">

                <xsl:value-of select="/cvbook/@index"/>

            </fo:block>

            <fo:table table-layout="fixed" width="100%">
                <fo:table-column column-width="80%"/>
                <fo:table-column column-width="20%"/>

                <fo:table-body>

                    <xsl:for-each select="/cvbook/cvs/cvgroup/cv">

                        <xsl:sort select="@lastname"/>
                        <xsl:sort select="@firstname"/>

                        <fo:table-row>
                            <fo:table-cell>

                                <fo:block>
                                    <xsl:value-of select="@firstname"/><xsl:text> </xsl:text><xsl:value-of select="@lastname"/>
                                </fo:block>

                            </fo:table-cell>

                            <fo:table-cell>
                                <fo:block>
                                    <fo:page-number-citation>
                                        <xsl:attribute name="ref-id">
                                            <xsl:value-of select="@id"/>
                                        </xsl:attribute>
                                    </fo:page-number-citation>
                                </fo:block>
                            </fo:table-cell>

                        </fo:table-row>
                    </xsl:for-each>
                </fo:table-body>
            </fo:table>
        </fo:block>
    </xsl:template>

</xsl:stylesheet>
