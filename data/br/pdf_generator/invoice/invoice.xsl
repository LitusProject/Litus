<!--
	XSL Stylsheet for Invoices

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
	
	<xsl:template match="invoice">
	    <fo:root font-size="10pt">
	        <fo:layout-master-set>
	            <fo:simple-page-master master-name="page-master"
	                      page-height="297mm" page-width="210mm"
	                      margin-top="8mm" margin-bottom="10mm"
	                      margin-left="20mm" margin-right="20mm">
	                <fo:region-body margin-bottom="8mm"/>
	                <fo:region-after region-name="footer-block" extent="10mm"/>
	            </fo:simple-page-master>
	
	            <fo:page-sequence-master master-name="document">
	               <fo:repeatable-page-master-alternatives>
	                   <fo:conditional-page-master-reference odd-or-even="even"
	                     master-reference="page-master"/>
	                   <fo:conditional-page-master-reference odd-or-even="odd"
	                     master-reference="page-master"/>
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
	                <fo:block margin-left="20px" margin-right="20px">
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
	                                <fo:table-cell margin-left="0px">
	                                    <fo:block text-align="right" padding-after="30px" margin-top="30px" font-size="16pt" font-weight="bold" color="#DDDDDD">
	                                        <xsl:call-template name="invoice_all_u"/>
	                                    </fo:block>
	                                    <fo:block>
	                                        <fo:block font-weight="bold" font-size="8pt"><xsl:call-template name="invoice_address_u"/></fo:block>
	                                    </fo:block>
	                                </fo:table-cell>
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
	
	                    <fo:block padding-after="10px"/>
	
	                    <xsl:apply-templates select="title"/>
	
	                    <fo:block padding-after="10px"/>
	
	                    <xsl:apply-templates select="entries"/>
	                    <xsl:apply-templates select="total"/>
	                    <fo:block padding-after="15px"/>
	                    <xsl:apply-templates select="sub_entries"/>
	                </fo:block>
	            </fo:flow>
	        </fo:page-sequence>
	    </fo:root>
	</xsl:template>
	
	<xsl:template match="title">
	    <fo:table table-layout="fixed" width="100%">
	        <fo:table-column column-width="20%"/>
	        <fo:table-column column-width="20%"/>
	        <fo:table-column column-width="20%"/>
	        <fo:table-column column-width="20%"/>
	        <fo:table-column column-width="20%"/>
	
	        <fo:table-body>
	            <fo:table-row background-color="#EEEEEE">
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="invoice_number_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="invoice_date_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="expiration_date_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="vat_client_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="reference_u"/></fo:block>
	                </fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:value-of select="invoice_number"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:value-of select="invoice_date"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:value-of select="expiration_date"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:value-of select="vat_client"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:value-of select="reference"/></fo:block>
	                </fo:table-cell>
	            </fo:table-row>
	        </fo:table-body>
	    </fo:table>
	</xsl:template>
	
	<!-- FOOTER -->
	
	<xsl:template match="footer">
	    <fo:table table-layout="fixed" width="100%">
	        <fo:table-column column-width="30%"/>
	        <fo:table-column column-width="40%"/>
	        <fo:table-column column-width="30%"/>
	
	        <fo:table-body>
	            <fo:table-row>
	            	<fo:table-cell><fo:block>TODO</fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block>FOOTER</fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block>INVOICE</fo:block></fo:table-cell>
	            </fo:table-row>
	            <!-- <xsl:apply-templates select="f_row"/> -->
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
	
	<xsl:template match="middle">
	    <fo:table-cell>
	        <fo:block text-align="center">
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
	
	<!-- /FOOTER -->
	
	<xsl:template match="entries">
	    <fo:table table-layout="fixed" width="100%" border-style="solid" border-width="1px">
	        <fo:table-column column-width="80%"/>
	        <fo:table-column column-width="15%"/>
	        <fo:table-column column-width="5%"/>
	
	        <fo:table-body>
	            <fo:table-row background-color="#EEEEEE">
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="description_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="total_excl_short"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:text> </xsl:text></fo:block>
	                </fo:table-cell>
	            </fo:table-row>
	            <xsl:apply-templates select="entry|empty_line"/>
	        </fo:table-body>
	    </fo:table>
	</xsl:template>
	
	<xsl:template match="empty_line">
	    <fo:table-row>
	        <fo:table-cell display-align="center" text-align="left" margin-right="0px" margin-left="5px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <fo:block><xsl:text>&#x00A0;</xsl:text></fo:block>
	        </fo:table-cell>
	        <fo:table-cell display-align="center" text-align="right" margin-right="5px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <fo:block><xsl:text>&#x00A0;</xsl:text></fo:block>
	        </fo:table-cell>
	        <fo:table-cell display-align="center" text-align="center" margin-right="0px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <fo:block><xsl:text>&#x00A0;</xsl:text></fo:block>
	        </fo:table-cell>
	    </fo:table-row>
	</xsl:template>
	
	<xsl:template match="entry">
	    <fo:table-row>
	        <fo:table-cell display-align="center" text-align="left" margin-right="0px" margin-left="5px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <xsl:apply-templates select="description"/>
	        </fo:table-cell>
	        <fo:table-cell display-align="center" text-align="right" margin-right="5px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <xsl:apply-templates select="price"/>
	        </fo:table-cell>
	        <fo:table-cell display-align="center" text-align="center" margin-right="0px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <fo:block><xsl:value-of select="vat_type"/></fo:block>
	        </fo:table-cell>
	    </fo:table-row>
	</xsl:template>
	
	<xsl:template match="description|price|price_excl|price_vat|price_incl">
	    <fo:block><xsl:apply-templates/></fo:block>
	</xsl:template>
	
	<xsl:template match="total">
	    <fo:table table-layout="fixed" width="100%">
	        <fo:table-column column-width="60%"/>
	        <fo:table-column column-width="20%"/>
	        <fo:table-column column-width="15%"/>
	        <fo:table-column column-width="5%"/>
	
	        <fo:table-body>
	            <fo:table-row>
	                <fo:table-cell margin-left="0px" padding-before="2px" display-align="center">
	                    <fo:block><xsl:value-of select="vat_type_explanation"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" background-color="#EEEEEE" margin-right="0px" margin-left="0px" padding-before="2px" display-align="center">
	                    <fo:block><xsl:call-template name="total_excl_full"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" text-align="right" display-align="center" margin-right="5px" padding-before="2px" margin-left="0px">
	                    <xsl:apply-templates select="price_excl"/>
	                </fo:table-cell>
	                <fo:table-cell><fo:block>&#x00A0;</fo:block></fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	                <fo:table-cell><fo:block>&#x00A0;</fo:block></fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" background-color="#EEEEEE" margin-right="0px" margin-left="0px" padding-before="2px" display-align="center">
	                    <fo:block><xsl:call-template name="vat_all_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" text-align="right" display-align="center" margin-right="5px" padding-before="2px" margin-left="0px">
	                    <xsl:apply-templates select="price_vat"/>
	                </fo:table-cell>
	                <fo:table-cell><fo:block>&#x00A0;</fo:block></fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	                <fo:table-cell><fo:block>&#x00A0;</fo:block></fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" background-color="#EEEEEE" margin-right="0px" margin-left="0px" padding-before="2px" display-align="center">
	                    <fo:block><xsl:call-template name="to_pay_all_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" text-align="right" display-align="center" margin-right="5px" padding-before="2px" margin-left="0px">
	                    <xsl:apply-templates select="price_incl"/>
	                </fo:table-cell>
	                <fo:table-cell><fo:block>&#x00A0;</fo:block></fo:table-cell>
	            </fo:table-row>
	        </fo:table-body>
	    </fo:table>
	</xsl:template>
	
	<xsl:template match="sub_entries">
	    <fo:block text-align="justify"><xsl:apply-templates/></fo:block>
	</xsl:template>

</xsl:stylesheet>
