<!--
  XSL Stylesheet for Events

  @author Kristof Mariën <kristof.marien@litus.cc>
  @author Pieter Maene <pieter.maene@litus.cc>
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

	<xsl:template match="event">
	    <fo:root font-size="10pt">
	        <fo:layout-master-set>
	            <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="55mm" margin-bottom="40mm" margin-left="15mm" margin-right="15mm">
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

	        <xsl:choose>
	        	<xsl:when test="count($shifts) != 0">
	                <fo:page-sequence master-reference="document">
	                	<fo:static-content flow-name="header-block">
	                		<fo:block>
	        		            <xsl:call-template name="header"/>
	        		    	</fo:block>
	        	        </fo:static-content>
	                	<fo:flow flow-name="xsl-region-body">
	                        <fo:block>
	                        	<xsl:apply-templates select="shifts"/>
	                        </fo:block>
	                    </fo:flow>
	                </fo:page-sequence>
	            </xsl:when>
	        </xsl:choose>
	    </fo:root>
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
	                    <fo:block text-align="left" font-size="12pt"><xsl:call-template name="shift_list"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell>
	                    <fo:block text-align="right" padding-before="5mm" font-style="italic" font-weight="bold" font-size="24pt"></fo:block>
						<fo:block text-align="right" font-style="italic" font-weight="bold" font-size="12pt">
	                    	<xsl:call-template name="event_name"/>
	                    </fo:block>
	                    <fo:block text-align="right" font-size="9pt">
	                    	<xsl:call-template name="event_date"/>
	                   </fo:block>
	                </fo:table-cell>
	            </fo:table-row>
	        </fo:table-body>
	    </fo:table>
	</xsl:template>

	<xsl:template match="shifts">
		<xsl:apply-templates select="shift"/>
	</xsl:template>

	<xsl:template match="shift">
	    <xsl:choose>
        	<xsl:when test="count(people/*) != 0">
        		<fo:block text-align="left" font-size="12pt" font-weight="bold" margin-bottom="1mm">
					<xsl:apply-templates select="date"/>—<xsl:apply-templates select="name"/>
				</fo:block>

                <fo:table table-layout="fixed" width="100%" margin-bottom="5mm">
			        <fo:table-column column-width="55%"/>
			        <fo:table-column column-width="30%"/>
			        <fo:table-column column-width="15%"/>

			        <fo:table-header>
					    <fo:table-row>
					        <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm"	border-style="solid" border-width="0.5mm" border-color="black">
					            <fo:block text-align="left" font-weight="bold"><xsl:call-template name="name"/></fo:block>
					        </fo:table-cell>
					        <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
					            <fo:block text-align="left" font-weight="bold"><xsl:call-template name="phone_number"/></fo:block>
					        </fo:table-cell>
					        <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
					            <fo:block text-align="center" font-weight="bold"><xsl:call-template name="responsible"/></fo:block>
					        </fo:table-cell>
					    </fo:table-row>
					</fo:table-header>

			        <fo:table-body>
			            <xsl:apply-templates select="people"/>
			        </fo:table-body>
			    </fo:table>
            </xsl:when>
            <xsl:otherwise>
            	<fo:block text-align="left" font-size="12pt" font-weight="bold" margin-bottom="5mm">
					<xsl:apply-templates select="date"/>—<xsl:apply-templates select="name"/>
				</fo:block>
            </xsl:otherwise>
        </xsl:choose>
	</xsl:template>

	<xsl:template match="people">
		<xsl:apply-templates select="person"/>
	</xsl:template>

	<xsl:template match="person">
		<fo:table-row>
	        <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
	            <fo:block text-align="left"><xsl:apply-templates select="name"/></fo:block>
	        </fo:table-cell>
	        <fo:table-cell padding-start="2mm" padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
	            <fo:block text-align="left"><xsl:apply-templates select="phone_number"/></fo:block>
	        </fo:table-cell>
	        <fo:table-cell padding-before="1mm" padding-after="1mm" border-style="solid" border-width="0.5mm" border-color="black">
	            <fo:block text-align="center" font-family="Arial">
	            	<xsl:choose>
	            		<xsl:when test="responsible=1">
	            			<xsl:call-template name="printCheckedBox"/>
	            	    </xsl:when>
	            	    <xsl:when test="responsible!=1">
	            			<xsl:call-template name="printBox"/>
	            	    </xsl:when>
	            	</xsl:choose>
	            </fo:block>
	        </fo:table-cell>
	    </fo:table-row>
	</xsl:template>

	<xsl:param name="shifts" select="/event/shifts/*"/>

	<xsl:template name="union_name" match="union_name">
	    <xsl:value-of select="/event/our_union/name"/>
	</xsl:template>

	<xsl:template name="event_name" match="event_name">
	    <xsl:value-of select="/event/@name"/>
	</xsl:template>

	<xsl:template name="event_date" match="event_date">
	    <xsl:value-of select="/event/@date"/>
	</xsl:template>

	<xsl:template name="printCheckedBox">
		<xsl:param name="imageheight">8</xsl:param>
		<xsl:param name="imagewidth">8</xsl:param>
	    <fo:instream-foreign-object>
	          <svg:svg width="{$imagewidth}" height="{$imageheight}" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
	                <svg:g style="fill:none; stroke:black;stroke-width:3">
	                      <svg:rect x="0" y="0" width="20" height="20"/>
	                      <svg:line x1="4" y1="10" x2="10" y2="17"/>
	                      <svg:line x1="10" y1="17" x2="17" y2="3"/>
	                </svg:g>
	          </svg:svg>
	    </fo:instream-foreign-object>
	</xsl:template>
	<xsl:template name="printBox">
	    <xsl:param name="imageheight">8</xsl:param>
	    <xsl:param name="imagewidth">8</xsl:param>
	    <fo:instream-foreign-object>
	          <svg:svg width="{$imagewidth}" height="{$imageheight}" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
	                <svg:g style="fill:none; stroke:black;stroke-width:3">
	                      <svg:rect x="0" y="0" width="20" height="20"/>
	                </svg:g>
	          </svg:svg>
	    </fo:instream-foreign-object>
	</xsl:template>
	<xsl:template name="printXBox">
	    <xsl:param name="imageheight">8</xsl:param>
	    <xsl:param name="imagewidth">8</xsl:param>
	    <fo:instream-foreign-object>
	          <svg:svg width="{$imagewidth}" height="{$imageheight}" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet" xml:space="preserve">
	                <svg:g style="fill:none; stroke:black;stroke-width:3">
	                      <svg:rect x="0" y="0" width="20" height="20"/>
	                      <svg:line x1="3" y1="17" x2="17" y2="3"/>
	                      <svg:line x1="3" y1="3" x2="17" y2="17"/>
	                </svg:g>
	          </svg:svg>
	    </fo:instream-foreign-object>
	</xsl:template>
</xsl:stylesheet>