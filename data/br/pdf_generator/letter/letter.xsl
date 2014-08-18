<!--
    XSL stylsheet for Letters

    @author Bram Gotink <bram.gotink@litus.cc>
-->

<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:import href="../../../pdf_generator/essentials.xsl"/>
    <xsl:import href="../../../pdf_generator/company.xsl"/>

    <xsl:import href="../../../pdf_generator/our_union/essentials.xsl"/>
    <xsl:import href="../../../pdf_generator/our_union/full_no_logo.xsl"/>

    <xsl:import href="i18n/default.xsl"/>


    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="letter">
        <fo:root font-size="10pt">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="8mm" margin-bottom="10mm" margin-left="20mm" margin-right="20mm">
                    <fo:region-body margin-bottom="8mm"/>
                    <fo:region-after region-name="footer-block" extent="10mm"/>
                </fo:simple-page-master>

                <fo:page-sequence-master master-name="document">
                   <fo:repeatable-page-master-alternatives>
                       <fo:conditional-page-master-reference odd-or-even="even" master-reference="page-master"/>
                       <fo:conditional-page-master-reference odd-or-even="odd" master-reference="page-master"/>
                   </fo:repeatable-page-master-alternatives>
                </fo:page-sequence-master>
            </fo:layout-master-set>

            <fo:page-sequence master-reference="document">
                <fo:static-content flow-name="footer-block">
                    <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" border-before-color="black" border-before-style="solid" border-before-width="0.15mm" color="grey" text-align="center">
                        <xsl:apply-templates select="footer"/>
                    </fo:block>
                </fo:static-content>
                <fo:flow flow-name="xsl-region-body">
                    <fo:block margin-left="20px" margin-right="20px" margin-top="20px">
                        <fo:table table-layout="fixed" width="100%">
                            <fo:table-column column-width="60%"/>
                            <fo:table-column column-width="40%"/>

                            <fo:table-body>
                                <fo:table-row>
                                    <fo:table-cell display-align="after" margin-left="0px">
                                        <fo:block text-align="left">
                                            <xsl:apply-templates select="our_union/logo">
                                                <xsl:with-param name="width"><xsl:text>60%</xsl:text></xsl:with-param>
                                            </xsl:apply-templates>
                                        </fo:block>
                                    </fo:table-cell>
                                    <fo:table-cell margin-left="0px"><fo:block/></fo:table-cell>
                                </fo:table-row>
                                <fo:table-row>
                                    <fo:table-cell margin-left="0px">
                                        <fo:block><xsl:apply-templates select="our_union"/></fo:block>
                                    </fo:table-cell>
                                    <fo:table-cell margin-left="0px">
                                        <fo:block><xsl:apply-templates select="company"/></fo:block>
                                    </fo:table-cell>
                                </fo:table-row>
                            </fo:table-body>
                        </fo:table>

                        <fo:block padding-after="30px"/>

                        <fo:block padding-after="10px">
                            <xsl:call-template name="dear_u"/><xsl:text> </xsl:text><xsl:value-of select="company/contact_person/title"/><xsl:text> </xsl:text><xsl:value-of select="company/contact_person/last_name"/>
                        </fo:block>

                        <xsl:apply-templates select="paragraph"/>

                        <fo:block padding-after="20px"/>

                        <fo:block padding-after="20px"><xsl:call-template name="regards_u"/></fo:block>

                        <fo:block><xsl:apply-templates select="our_union/contact_person"/></fo:block>

                    </fo:block>
                </fo:flow>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>

    <xsl:template match="paragraph">
        <xsl:apply-templates/>
        <xsl:call-template name="br">
            <xsl:with-param name="space"><xsl:text>10px</xsl:text></xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="footer">
        <fo:table table-layout="fixed" width="100%">
            <fo:table-column column-width="50%"/>
            <fo:table-column column-width="50%"/>

            <fo:table-body>
                <xsl:apply-templates select="f_row"/>
            </fo:table-body>
        </fo:table>
    </xsl:template>

    <xsl:template match="f_row">
        <fo:table-row>
            <xsl:apply-templates/>
        </fo:table-row>
    </xsl:template>

    <xsl:template match="left">
        <fo:table-cell>
            <fo:block text-align="left">
                <xsl:apply-templates/>
            </fo:block>
        </fo:table-cell>
    </xsl:template>

    <xsl:template match="right">
        <fo:table-cell>
            <fo:block text-align="right">
                <xsl:apply-templates/>
            </fo:block>
        </fo:table-cell>
    </xsl:template>

</xsl:stylesheet>
