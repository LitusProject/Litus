<xsl:stylesheet
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
        xmlns:fo="http://www.w3.org/1999/XSL/Format"
        xmlns:svg="http://www.w3.org/2000/svg"
        >

    <xsl:import href="../../../pdf_generator/essentials.xsl"/>

    <xsl:import href="../../../pdf_generator/our_union/essentials.xsl"/>
    <xsl:import href="../../../pdf_generator/our_union/logo.xsl"/>
    <xsl:import href="../../../pdf_generator/company.xsl"/>
    <xsl:import href="i18n/default.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="companieslist">
        <fo:root font-size="10pt">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm"
                                       margin-top="55mm" margin-bottom="40mm" margin-left="15mm" margin-right="15mm">
                    <fo:region-body margin-bottom="8mm"/>
                    <fo:region-before region-name="header-block" extent="-35mm"/>
                    <fo:region-after region-name="footer-block" extent="0mm"/>
                </fo:simple-page-master>

                <fo:page-sequence-master master-name="document">
                    <fo:repeatable-page-master-alternatives>
                        <fo:conditional-page-master-reference odd-or-even="even" master-reference="page-master"/>
                        <fo:conditional-page-master-reference odd-or-even="odd" master-reference="page-master"/>
                    </fo:repeatable-page-master-alternatives>
                </fo:page-sequence-master>
            </fo:layout-master-set>

            <fo:page-sequence master-reference="document">
                <fo:static-content flow-name="header-block">
                    <fo:block>
                        <xsl:call-template name="header"/>
                    </fo:block>
                </fo:static-content>

                <xsl:choose>
                    <xsl:when test="count($companies) != 0">
                        <fo:flow flow-name="xsl-region-body">
                            <fo:block>
                                <xsl:apply-templates select="companies"/>
                            </fo:block>
                        </fo:flow>
                    </xsl:when>
                    <xsl:otherwise>
                        <fo:flow flow-name="xsl-region-body">
                            <fo:block text-align="left" font-style="italic" font-size="10pt"
                                      margin-bottom="1mm"></fo:block>
                        </fo:flow>
                    </xsl:otherwise>
                </xsl:choose>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>

    <xsl:template name="header" match="header">
        <fo:table table-layout="fixed" width="100%">
            <fo:table-column column-width="27%"/>
            <fo:table-column column-width="40%"/>
            <fo:table-column column-width="33%"/>

            <fo:table-body>
                <fo:table-row>
                    <fo:table-cell padding="3mm" border-end-color="black" border-end-style="solid"
                                   border-end-width="0.7mm">
                        <fo:block text-align="left" padding-end="5mm">
                            <xsl:apply-templates select="our_union"/>
                        </fo:block>
                    </fo:table-cell>
                    <fo:table-cell padding-start="3mm">
                        <fo:block text-align="left" padding-before="5mm" padding-after="2mm" font-size="17pt"
                                  font-weight="bold">
                            <xsl:call-template name="union_name"/>
                        </fo:block>
                        <fo:block text-align="left" font-size="12pt">
                            <xsl:call-template name="companieslist"/>
                        </fo:block>
                    </fo:table-cell>
                    <fo:table-cell>
                        <fo:block text-align="right" padding-before="5mm" font-style="italic" font-weight="bold"
                                  font-size="24pt"></fo:block>
                        <fo:block text-align="right" font-style="italic" font-weight="bold" font-size="12pt">
                            <xsl:call-template name="companieslist_name"/>
                        </fo:block>
                        <fo:block text-align="right" font-size="9pt">
                            <xsl:call-template name="companieslist_date"/>
                        </fo:block>
                    </fo:table-cell>
                </fo:table-row>
            </fo:table-body>
        </fo:table>
    </xsl:template>

    <xsl:template match="companies">
        <xsl:apply-templates select="company"/>
    </xsl:template>

    <xsl:template match="company">
        <fo:block text-align="left" font-size="12pt" font-weight="bold">
            <xsl:apply-templates select="name"/>
        </fo:block>
        <fo:block text-align="left" font-style="italic" font-size="10pt" margin-bottom="1mm">
            <xsl:call-template name="companyPhoneNumber"/>:
            <xsl:apply-templates select="companyPhone"/>
        </fo:block>
        <xsl:choose>
            <xsl:when test="count(users/*) != 0">
                <fo:table table-layout="fixed" width="100%" margin-bottom="5mm">
                    <fo:table-column column-width="30%"/>
                    <fo:table-column column-width="20%"/>
                    <fo:table-column column-width="30%"/>
                    <fo:table-column column-width="20%"/>
                    <fo:table-header>
                        <fo:table-row>
                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm"
                                           border-style="solid" border-width="0.5mm" border-color="black">
                                <fo:block text-align="left" font-weight="bold">
                                    <xsl:call-template name="name"/>
                                </fo:block>
                            </fo:table-cell>
                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm"
                                           border-style="solid" border-width="0.5mm" border-color="black">
                                <fo:block text-align="left" font-weight="bold">
                                    <xsl:call-template name="username"/>
                                </fo:block>
                            </fo:table-cell>
                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid"
                                           border-width="0.5mm" border-color="black">
                                <fo:block text-align="left" font-weight="bold">
                                    <xsl:call-template name="email"/>
                                </fo:block>
                            </fo:table-cell>
                            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid"
                                           border-width="0.5mm" border-color="black">
                                <fo:block text-align="left" font-weight="bold">
                                    <xsl:call-template name="userPhoneNumber"/>
                                </fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </fo:table-header>

                    <fo:table-body>
                        <xsl:apply-templates select="users"/>
                    </fo:table-body>
                </fo:table>
            </xsl:when>
            <xsl:otherwise>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="users">
        <xsl:apply-templates select="person"/>
    </xsl:template>

    <xsl:template match="person">
        <fo:table-row>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid"
                           border-width="0.5mm" border-color="black">
                <fo:block text-align="left">
                    <xsl:apply-templates select="name"/>
                </fo:block>
            </fo:table-cell>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid"
                           border-width="0.5mm" border-color="black">
                <fo:block text-align="left">
                    <xsl:apply-templates select="username"/>
                </fo:block>
            </fo:table-cell>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm"
                           border-color="black">
                <fo:block text-align="left" font-family="Arial">
                    <xsl:apply-templates select="email"/>
                </fo:block>
            </fo:table-cell>
            <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm"
                           border-color="black">
                <fo:block text-align="left" font-family="Arial">
                    <xsl:apply-templates select="userPhone"/>
                </fo:block>
            </fo:table-cell>
        </fo:table-row>
    </xsl:template>

    <xsl:param name="companies" select="/companieslist/companies/*"/>

    <xsl:template name="union_name" match="union_name">
        <xsl:value-of select="/companieslist/our_union/name"/>
    </xsl:template>

    <xsl:template name="companieslist_name" match="companieslist_name">
        <xsl:value-of select="/companieslist/@name"/>
    </xsl:template>

    <xsl:template name="companieslist_date" match="companieslist_date">
        <xsl:value-of select="/companieslist/@date"/>
    </xsl:template>
</xsl:stylesheet>
