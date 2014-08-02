<!--
	XSL Stylsheet for Contracts

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
	<xsl:import href="../../../pdf_generator/our_union/simple_with_logo.xsl"/>

	<xsl:import href="i18n/default.xsl"/>

	<xsl:output method="xml" indent="yes"/>

	<xsl:template match="contract">
	    <fo:root font-size="10pt">
	        <fo:layout-master-set>
	            <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="8mm" margin-bottom="10mm" margin-left="20mm" margin-right="20mm">
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
	                <fo:block margin-left="20px" margin-right="5px">
	                    <fo:table table-layout="fixed" width="100%" margin-left="5px">
	                        <fo:table-column column-width="50%"/>
	                        <fo:table-column column-width="50%"/>

	                        <fo:table-body>
	                            <fo:table-row>
	                                <fo:table-cell>
	                                    <fo:block text-align="left"><xsl:apply-templates select="our_union"/></fo:block>
	                                </fo:table-cell>
	                                <fo:table-cell>
	                                    <fo:block font-size="8pt" margin-top="15px" text-align="right"><xsl:call-template name="date_and_location"/></fo:block>
	                                </fo:table-cell>
	                            </fo:table-row>
	                        </fo:table-body>
	                    </fo:table>

	                    <fo:block padding-after="10px"/>

	                    <xsl:apply-templates select="title"/>

	                    <fo:block padding-after="10px"/>

	                    <fo:table table-layout="fixed" width="100%">
	                        <fo:table-column column-width="15%"/>
	                        <fo:table-column column-width="85%"/>

	                        <fo:table-body>
	                            <fo:table-row>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:call-template name="between_u"/>
	                                    </fo:block>
	                                </fo:table-cell>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:apply-templates select="company"/>
	                                    </fo:block>
	                                    <fo:block>
	                                        <xsl:call-template name="represented_by">
	                                            <xsl:with-param name="name" select="company/@contact_person"/>
	                                        </xsl:call-template>
	                                    </fo:block>
	                                    <fo:block>
	                                        <xsl:call-template name="known_as">
	                                            <xsl:with-param name="alias"><xsl:call-template name="the_company"/></xsl:with-param>
	                                        </xsl:call-template>
	                                    </fo:block>
	                                    <fo:block padding-after="5px"/>
	                                </fo:table-cell>
	                            </fo:table-row>
	                            <fo:table-row>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:call-template name="and"/>
	                                    </fo:block>
	                                </fo:table-cell>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:apply-templates select="union_address"/>
	                                    </fo:block>
	                                    <fo:block>
	                                        <xsl:call-template name="represented_by">
	                                            <xsl:with-param name="name" select="our_union/@contact_person"/>
	                                        </xsl:call-template>
	                                    </fo:block>
	                                    <fo:block>
	                                        <xsl:call-template name="known_as">
	                                            <xsl:with-param name="alias" select="our_union/@short_name"/>
	                                        </xsl:call-template>
	                                    </fo:block>
	                                </fo:table-cell>
	                            </fo:table-row>
	                        </fo:table-body>
	                    </fo:table>
	                    <fo:block padding-before="8px" padding-after="8px">
	                        <xsl:call-template name="was_agreed"/>
	                    </fo:block>
	                    <fo:block padding-after="8px">
	                        <xsl:apply-templates select="entries"/>
	                    </fo:block>
	                    <fo:block padding-after="20px">
	                        <xsl:apply-templates select="sub_entries"/>
	                    </fo:block>
	                    <fo:table table-layout="fixed" width="100%">
	                        <fo:table-column column-width="35%"/>
	                        <fo:table-column column-width="30%"/>
	                        <fo:table-column column-width="35%"/>

	                        <fo:table-body>
	                            <fo:table-row>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:call-template name="for_u"/><xsl:text> </xsl:text><xsl:call-template name="the_company"/><xsl:text>,</xsl:text>
	                                        <fo:block />
	                                        <xsl:value-of select="company/@contact_person"/>
	                                    </fo:block>
	                                </fo:table-cell>
	                                <fo:table-cell>
	                                   <fo:block/>
	                                </fo:table-cell>
	                                <fo:table-cell>
	                                    <fo:block>
	                                        <xsl:call-template name="for_u"/><xsl:text> </xsl:text><xsl:value-of select="our_union/@short_name"/><xsl:text>,</xsl:text>
	                                        <fo:block/>
	                                        <xsl:value-of select="our_union/@contact_person"/>
	                                    </fo:block>
	                                </fo:table-cell>
	                            </fo:table-row>
	                        </fo:table-body>
	                    </fo:table>
	                </fo:block>
	            </fo:flow>
	        </fo:page-sequence>
	    </fo:root>
	</xsl:template>

	<xsl:template match="entries">
	    <fo:list-block margin-left="10px" margin-right="10px">
	        <xsl:apply-templates select="entry"/>
	    </fo:list-block>
	</xsl:template>

	<xsl:template match="entry">
	    <fo:list-item>
			<fo:list-item-label>
        	<fo:block font-family="helvetica">
				<!-- <xsl:variable name="entry_cnt" select="count(ancestor::entry)"/> -->
	    		<!-- <xsl:value-of select="count(ancestor::entry)"/> -->

			<xsl:choose>
	          	<xsl:when test="(count(ancestor::entry) mod 2) = 0">
	            	&#x2022;
	          	</xsl:when>
	    	  	<xsl:otherwise>
	            	o
	      		</xsl:otherwise>
	        </xsl:choose>


	        </fo:block>
	        </fo:list-item-label>
	        <fo:list-item-body>
	            <fo:block text-align="justify" margin-left="30px" padding-after="3px"><xsl:apply-templates/></fo:block>
	        </fo:list-item-body>
	    </fo:list-item>
	</xsl:template>

	<xsl:template match="sub_entries">
	    <fo:block><xsl:apply-templates/></fo:block>
	</xsl:template>

	<xsl:template match="company_name">
	    <xsl:value-of select="/contract/company/name"/>
	</xsl:template>

	<xsl:template match="title">
	    <fo:block background-color="#DDDDDD" font-size="16pt" text-align="center" padding-top="5px" padding-bottom="5px" padding-left="0px" padding-right="0px" margin-left="0px" margin-right="0px">
	        <xsl:apply-templates/>
	    </fo:block>
	</xsl:template>

	<xsl:template name="date" match="date">
	    <xsl:value-of select="/contract/@date"/>
	</xsl:template>

	<xsl:template name="location" match="location">
	    <xsl:value-of select="/contract/@location"/>
	</xsl:template>

	<xsl:template name="date_and_location" match="date_and_location">
	    <xsl:call-template name="location"/><xsl:text>, </xsl:text><xsl:call-template name="date"/>
	</xsl:template>

	<xsl:template match="footer">
	    <fo:table table-layout="fixed" width="100%">

	        <fo:table-body>
	            <fo:table-row>
	            	<fo:table-cell><fo:block text-align="left">BTW: BE 0479.482.282 <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block>Vlaams Technische Kring <xsl:apply-templates select="center"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block text-align="right">Tel: +32 (0)16 20.00.97 <xsl:apply-templates select="right"/></fo:block></fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	            	<fo:table-cell><fo:block text-align="left">KBC: 745-175900-11 <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block>Faculteitskring Ingenieurswetenschappen <xsl:apply-templates select="center"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block text-align="right">Fax: +32 (0)16 20.65.29 <xsl:apply-templates select="right"/></fo:block></fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	            	<fo:table-cell><fo:block text-align="left">IBAN: BE30 7450 1759 0011 <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block>aan de K.U.Leuven <xsl:apply-templates select="center"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block text-align="right">http://www.vtk.be <xsl:apply-templates select="right"/></fo:block></fo:table-cell>
	            </fo:table-row>
	            <fo:table-row>
	            	<fo:table-cell><fo:block text-align="left">BIC: KREDBAB <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block><xsl:apply-templates select="center"/></fo:block></fo:table-cell>
	            	<fo:table-cell><fo:block text-align="right">bedrijvenrelaties@vtk.be <xsl:apply-templates select="right"/></fo:block></fo:table-cell>
	            </fo:table-row>
	            <!-- <xsl:apply-templates select="f_row"/> -->
	        </fo:table-body>
	    </fo:table>
	</xsl:template>

</xsl:stylesheet>
