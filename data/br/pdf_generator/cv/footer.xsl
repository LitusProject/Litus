<!--
    XSL Stylsheet for CV Book

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template name="footer-even">
        <fo:block font-size="12pt" font-family="sans-serif" padding-before="0.5mm"
                border-before-color="black" border-before-style="solid" border-before-width="0.15mm">
            <fo:table table-layout="fixed">
                <fo:table-column column-width="64mm"/>
                <fo:table-column column-width="34mm"/>
                <fo:table-column column-width="47mm"/>
                <fo:table-column column-width="17mm"/>
                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell padding-left="1mm">
                            <fo:block text-align="left" padding-before="1.9mm">
                                <fo:retrieve-marker retrieve-class-name="footer-text" retrieve-boundary="document"/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="center" padding-before="1.9mm">
                                <fo:page-number/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell padding-right="2mm">
                            <fo:block text-align="right" padding-right="1mm">
                                <fo:external-graphic content-width="8.9mm">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="/cvbook/@logo"/>
                                    </xsl:attribute>
                                </fo:external-graphic>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="right" padding-before="1.9mm">
                                CV-Boek
                            </fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>
            </fo:table>
        </fo:block>
    </xsl:template>

    <xsl:template name="footer-odd">
        <fo:block font-size="12pt" font-family="sans-serif" padding-before="0.5mm"
                border-before-color="black" border-before-style="solid" border-before-width="0.15mm">
            <fo:table table-layout="fixed">
                <fo:table-column column-width="11mm"/>
                <fo:table-column column-width="53mm"/>
                <fo:table-column column-width="34mm"/>
                <fo:table-column column-width="64mm"/>
                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell padding-left="1mm">
                            <fo:block>
                                <fo:external-graphic content-width="8.9mm">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="/cvbook/@logo"/>
                                    </xsl:attribute>
                                </fo:external-graphic>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="left" padding-before="1.9mm">
                                CV-Boek
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="center" padding-before="1.9mm">
                                <fo:page-number/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="right" padding-before="1.9mm">
                                <fo:retrieve-marker retrieve-class-name="footer-text" retrieve-boundary="document"/>
                            </fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>
            </fo:table>
        </fo:block>
    </xsl:template>


</xsl:stylesheet>