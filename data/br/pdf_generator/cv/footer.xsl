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
        <fo:block padding-top="{$footer-top-padding}mm"
            border-top-color="black"
            border-top-style="solid"
            border-top-width="0.15mm">

            <fo:table table-layout="fixed">
                <!-- Section title -->
                <fo:table-column column-width="{$footer-text-width}mm"/>

                <!-- Page number -->
                <fo:table-column column-width="{$page-width}mm - 2 * {$margin-x}mm - 2 * {$footer-text-width}mm"/>

                <!-- Padding column -->
                <fo:table-column column-width="{$footer-text-width}mm - {$logo-width}mm - {$logo-text-margin}mm - {$logo-text-width}mm"/>
                <!-- Logo and generic title -->
                <fo:table-column column-width="{$logo-width}mm + {$logo-text-margin}mm"/>
                <fo:table-column column-width="{$logo-text-width}mm"/>
                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block text-align="left" padding-top="{$footer-text-top-padding}mm">
                                <fo:retrieve-marker retrieve-class-name="footer-text" retrieve-boundary="document"/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="center" padding-top="{$footer-text-top-padding}mm">
                                <fo:page-number/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block/>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="left">
                                <fo:external-graphic content-width="{$logo-width}mm">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="/cvbook/@logo"/>
                                    </xsl:attribute>
                                </fo:external-graphic>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="left" padding-top="{$footer-text-top-padding}mm">
                                CV-Boek
                            </fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>
            </fo:table>
        </fo:block>
    </xsl:template>

    <xsl:template name="footer-odd">
        <fo:block padding-top="{$footer-top-padding}mm"
            border-top-color="black"
            border-top-style="solid"
            border-top-width="0.15mm">

            <fo:table table-layout="fixed">
                <!-- Logo and generic title -->
                <fo:table-column column-width="{$logo-width}mm + {$logo-text-margin}mm"/>
                <fo:table-column column-width="{$logo-text-width}mm"/>
                <!-- Padding column -->
                <fo:table-column column-width="{$footer-text-width}mm - {$logo-width}mm - {$logo-text-margin}mm - {$logo-text-width}mm"/>

                <!-- Page number -->
                <fo:table-column column-width="{$page-width}mm - 2 * {$margin-x}mm - 2 * {$footer-text-width}mm"/>

                <!-- Section title -->
                <fo:table-column column-width="{$footer-text-width}mm"/>
                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block>
                                <fo:external-graphic content-width="{$logo-width}mm">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="/cvbook/@logo"/>
                                    </xsl:attribute>
                                </fo:external-graphic>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="left" padding-top="{$footer-text-top-padding}mm">
                                CV-Boek
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block/>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="center" padding-top="{$footer-text-top-padding}mm">
                                <fo:page-number/>
                            </fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block text-align="right" padding-top="{$footer-text-top-padding}mm">
                                <fo:retrieve-marker retrieve-class-name="footer-text" retrieve-boundary="document"/>
                            </fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>
            </fo:table>
        </fo:block>
    </xsl:template>


</xsl:stylesheet>