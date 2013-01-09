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
                <xsl:value-of select="/cvbook/@toc"/>
            </fo:marker>

            <!-- Title -->
            <fo:block
                line-height="{$title-line-height}"
                font-size="{$title-font-size}pt">

                <xsl:value-of select="/cvbook/@toc"/>

            </fo:block>

            <fo:table>
                <fo:table-column column-width="80%"/>
                <fo:table-column column-width="20%"/>

                <fo:table-body>

                    <xsl:for-each select="/cvbook/cvgroup">
                        <fo:table-row>
                            <fo:table-cell>

                                <fo:block>
                                    <xsl:value-of select="@name"/>
                                </fo:block>

                            </fo:table-cell>

                            <fo:table-cell>
                                <fo:block>
                                    <fo:page-number-citation>
                                        <xsl:attribute name="ref-id">
                                            <xsl:value-of select="@name"/>
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
