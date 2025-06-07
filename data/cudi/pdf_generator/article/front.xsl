<!--
  XSL Stylesheet for Front page of an article

  @author Kristof Mariën <kristof.marien@litus.cc>
-->

<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:svg="http://www.w3.org/2000/svg"
>

    <xsl:import href="../../../pdf_generator/barcode/upc-ean.xsl"/>
	<xsl:import href="../../../pdf_generator/our_union/essentials.xsl"/>
	<xsl:import href="../../../pdf_generator/our_union/logo.xsl"/>

	<xsl:import href="i18n/default.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="article">

        <fo:root>

            <fo:layout-master-set>
                <fo:simple-page-master master-name="article_front" page-height="297mm" page-width="210mm" margin-top="20mm" margin-bottom="20mm" margin-right="20mm">
                    <xsl:attribute name="margin-left">
                        <xsl:choose>
                            <xsl:when test="./@binding='none'">20mm</xsl:when>
                            <xsl:otherwise>30mm</xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <fo:region-body margin-top="0mm" margin-bottom="0mm" margin-left="0mm" margin-right="0mm"/>
                    <fo:region-before extent="0cm"/>
                    <fo:region-after extent="0cm"/>
                </fo:simple-page-master>
            </fo:layout-master-set>

            <fo:page-sequence master-reference="article_front">
                <fo:static-content flow-name="xsl-region-before"><fo:block/></fo:static-content>
                <fo:static-content flow-name="xsl-region-after"><fo:block/></fo:static-content>

                <fo:flow flow-name="xsl-region-body">

                    <fo:block-container position="absolute" top="180mm" left="0mm" width="170mm" height="100mm">
                        <fo:block font-family="sans-serif" font-size="24pt" font-weight="bold" text-align="center" space-after="20mm">
                            <xsl:apply-templates select="title"/>
                        </fo:block>
                        <fo:block font-family="sans-serif" font-size="15pt" text-align="center" space-after="40mm">
                            <xsl:apply-templates select="authors"/>
                        </fo:block>
                    </fo:block-container>

                    <fo:block-container position="absolute" top="237mm" left="90mm" width="80mm" height="20mm">
                        <fo:block font-family="sans-serif" font-size="12pt">
                            <fo:table table-layout="fixed" width="100%">
                                <fo:table-column column-number="1" column-width="20mm"/>
                                <fo:table-column column-number="2" column-width="60mm"/>

                                <fo:table-body>
                                    <xsl:for-each select="subjects/subject">
                                        <fo:table-row>
                                            <fo:table-cell display-align="baseline">
                                                <fo:block font-weight="bold">
                                                    <xsl:value-of select="code"/>
                                                </fo:block>
                                            </fo:table-cell>
                                            <fo:table-cell display-align="baseline">
                                                <fo:block
                                                        font-family="sans-serif"
                                                        font-size="12pt"
                                                        wrap-option="wrap"
                                                        white-space-collapse="true"
                                                        white-space-treatment="preserve"
                                                        linefeed-treatment="preserve"
                                                        hyphenate="true">
                                                    <xsl:value-of select="name"/>
                                                </fo:block>
                                            </fo:table-cell>
                                        </fo:table-row>
                                    </xsl:for-each>
                                </fo:table-body>
                            </fo:table>
                        </fo:block>
                    </fo:block-container>

                    <fo:block-container position="absolute" top="246mm" left="39mm" width="60mm" height="37mm">
                                        <fo:block font-family="sans-serif" font-size="10pt">
                                            <fo:block font-weight="bold"><xsl:value-of select="address/name"/></fo:block>
                                            <fo:block><xsl:value-of select="address/street"/></fo:block>
                                            <fo:block><xsl:value-of select="address/city"/></fo:block>
                                            <fo:block><xsl:value-of select="address/site"/></fo:block>
                                        </fo:block>
                    </fo:block-container>

                    <fo:block-container position="absolute" top="237mm" left="0mm" width="36mm" height="37mm">
                                        <fo:block font-family="sans-serif" font-size="12pt" font-weight="bold" text-align="center" space-after="2mm">
                                            <xsl:value-of select="price"/>&#160;<xsl:call-template name="euro"/>
                                        </fo:block>

                                        <fo:block>
                                            <fo:instream-foreign-object>
                                                <xsl:call-template name="barcode-EAN">
                                                    <xsl:with-param name="value" select="barcode"/>
                                                    <xsl:with-param name="height" select="'20mm'"/>
                                                    <xsl:with-param name="module" select="'.33mm'"/>
                                                </xsl:call-template>
                                            </fo:instream-foreign-object>
                                        </fo:block>
                    </fo:block-container>

                </fo:flow>

            </fo:page-sequence>

        </fo:root>
    </xsl:template>
</xsl:stylesheet>
