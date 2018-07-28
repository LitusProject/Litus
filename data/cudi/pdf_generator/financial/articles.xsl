<!--
  XSL Stylesheet for Stock overview

  @author Kristof Mariën <kristof.marien@litus.cc>
-->

<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
    xmlns:svg="http://www.w3.org/2000/svg"
>

    <xsl:import href="../../../pdf_generator/essentials.xsl"/>

    <xsl:import href="../../../pdf_generator/our_union/essentials.xsl"/>
    <xsl:import href="../../../pdf_generator/our_union/logo.xsl"/>

    <xsl:import href="i18n/default.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="financial">
        <fo:root font-size="10pt">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="55mm" margin-bottom="10mm" margin-left="15mm" margin-right="15mm">
                    <fo:region-body margin-bottom="8mm"/>
                    <fo:region-before region-name="header-block" extent="-35mm"/>
                </fo:simple-page-master>

                <fo:page-sequence-master master-name="document">
                   <fo:repeatable-page-master-alternatives>
                       <fo:conditional-page-master-reference odd-or-even="even" master-reference="page-master"/>
                       <fo:conditional-page-master-reference odd-or-even="odd" master-reference="page-master"/>
                   </fo:repeatable-page-master-alternatives>
                </fo:page-sequence-master>
            </fo:layout-master-set>

            <xsl:choose>
                <xsl:when test="count($internal_items_count) != 0">
                    <fo:page-sequence master-reference="document">
                        <fo:static-content flow-name="header-block">
                            <fo:block>
                                <xsl:call-template name="header"/>
                            </fo:block>
                        </fo:static-content>

                        <fo:flow flow-name="xsl-region-body">
                            <fo:block>
                                <fo:block text-align="left" font-size="15pt" font-weight="bold" padding-after="3pt">
                                    <xsl:call-template name="internal_articles"/>
                                </fo:block>
                            </fo:block>
                            <fo:block>
                                <fo:table table-layout="fixed" width="100%" font-size="8pt" border-style="solid" border-width="0.5mm" border-color="black">
                                    <fo:table-column column-width="15%"/>
                                    <fo:table-column column-width="49%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>

                                    <fo:table-header>
                                        <fo:table-row>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="barcode"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="title_author"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="ordered"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="delivered"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="sold"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="stock"/></fo:block>
                                            </fo:table-cell>
                                        </fo:table-row>
                                    </fo:table-header>

                                    <fo:table-body>
                                        <xsl:apply-templates select="internal_items"/>
                                    </fo:table-body>
                                </fo:table>
                            </fo:block>
                        </fo:flow>
                    </fo:page-sequence>
                </xsl:when>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="count($external_items_count) != 0">
                    <fo:page-sequence master-reference="document">
                        <fo:static-content flow-name="header-block">
                            <fo:block>
                                <xsl:call-template name="header"/>
                            </fo:block>
                        </fo:static-content>

                        <fo:flow flow-name="xsl-region-body">
                            <fo:block>
                                <fo:block text-align="left" font-size="15pt" font-weight="bold" padding-after="3pt">
                                    <xsl:call-template name="external_articles"/>
                                </fo:block>
                            </fo:block>
                            <fo:block>
                                <fo:table table-layout="fixed" width="100%" font-size="8pt" border-style="solid" border-width="0.5mm" border-color="black">
                                    <fo:table-column column-width="15%"/>
                                    <fo:table-column column-width="49%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>
                                    <fo:table-column column-width="9%"/>

                                    <fo:table-header>
                                        <fo:table-row>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="barcode"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="title_author"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="left" font-weight="bold"><xsl:call-template name="ordered"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="delivered"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="sold"/></fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                                                <fo:block text-align="center" font-weight="bold"><xsl:call-template name="stock"/></fo:block>
                                            </fo:table-cell>
                                        </fo:table-row>
                                    </fo:table-header>

                                    <fo:table-body>
                                        <xsl:apply-templates select="external_items"/>
                                    </fo:table-body>
                                </fo:table>
                            </fo:block>
                        </fo:flow>
                    </fo:page-sequence>
                </xsl:when>
            </xsl:choose>
        </fo:root>
    </xsl:template>

    <xsl:param name="internal_items_count" select="/financial/internal_items/*"/>

    <xsl:param name="external_items_count" select="/financial/external_items/*"/>

    <xsl:template name="date" match="date">
        <xsl:value-of select="/financial/@date"/>
    </xsl:template>

    <xsl:template name="mail" match="mail">
        <xsl:value-of select="/financial/cudi/mail"/>
    </xsl:template>

    <xsl:template name="phone" match="phone">
        <xsl:value-of select="/financial/cudi/phone"/>
    </xsl:template>

    <xsl:template name="union_name" match="union_name">
        <xsl:value-of select="/financial/our_union/name"/>
    </xsl:template>

    <xsl:template name="name" match="name">
        <xsl:value-of select="/financial/cudi/@name"/>
    </xsl:template>

    <xsl:template name="header" match="header">
        <fo:table table-layout="fixed" width="100%">
            <fo:table-column column-width="27%"/>
            <fo:table-column column-width="40%"/>
            <fo:table-column column-width="33%"/>

            <fo:table-body>
                <fo:table-row>
                    <fo:table-cell padding="3mm" border-end-color="black" border-end-style="solid" border-end-width="0.7mm">
                        <fo:block text-align="left" padding-end="5mm">
                            <xsl:apply-templates select="our_union"/>
                        </fo:block>
                    </fo:table-cell>
                    <fo:table-cell padding-start="3mm">
                        <fo:block text-align="left" padding-before="5mm" padding-after="2mm" font-size="17pt" font-weight="bold">
                            <xsl:call-template name="union_name"/>
                        </fo:block>
                        <fo:block text-align="left" font-size="12pt"><xsl:call-template name="name"/></fo:block>
                        <fo:block text-align="left" font-size="9pt"><xsl:call-template name="mail"/> - <xsl:call-template name="phone"/></fo:block>
                    </fo:table-cell>
                    <fo:table-cell>
                        <fo:block text-align="right" padding-before="9mm" font-style="italic" font-weight="bold" font-size="20pt">
                            <xsl:call-template name="financial"/>
                        </fo:block>
                        <fo:block text-align="right" font-size="9pt"><xsl:call-template name="date"/></fo:block>
                    </fo:table-cell>
                </fo:table-row>
            </fo:table-body>
        </fo:table>
    </xsl:template>

    <xsl:template match="internal_items">
        <xsl:apply-templates select="item"/>
    </xsl:template>

    <xsl:template match="external_items">
        <xsl:apply-templates select="item"/>
    </xsl:template>

    <xsl:template match="item">
        <fo:table-row>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="left"><xsl:apply-templates select="barcode"/></fo:block>
            </fo:table-cell>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="left"><xsl:apply-templates select="title"/> (<xsl:apply-templates select="author"/>)</fo:block>
            </fo:table-cell>
            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="center"><xsl:apply-templates select="ordered"/></fo:block>
            </fo:table-cell>
            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="center"><xsl:apply-templates select="delivered"/></fo:block>
            </fo:table-cell>
            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="center"><xsl:apply-templates select="sold"/></fo:block>
            </fo:table-cell>
            <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
                <fo:block text-align="center"><xsl:apply-templates select="stock"/></fo:block>
            </fo:table-cell>
        </fo:table-row>
    </xsl:template>

</xsl:stylesheet>