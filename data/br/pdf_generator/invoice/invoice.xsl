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
	                	<fo:block break-after='page'/>
	 					<xsl:apply-templates select="sale_conditions_nl"/>
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
	        <fo:table-column column-width="69%"/>
	        <fo:table-column column-width="15%"/>
	        <fo:table-column column-width="8%"/>
	        <fo:table-column column-width="8%"/>

	        <fo:table-body>
	            <fo:table-row background-color="#EEEEEE">
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="description_u"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="total_excl_short"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="product_amount"/></fo:block>
	                </fo:table-cell>
	                <fo:table-cell border-width="1px" border-style="solid" margin-left="0px" margin-right="0px" display-align="center" text-align="center" padding-before="2px">
	                    <fo:block><xsl:call-template name="VAT"/></fo:block>
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
	        <fo:table-cell display-align="center" text-align="right" margin-right="5px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <xsl:apply-templates select="amount"/>
	        </fo:table-cell>
	        <fo:table-cell display-align="center" text-align="center" margin-right="0px" margin-left="0px" padding-before="2px" border-right-style="solid" border-right-width="1px" border-left-style="solid" border-left-width="1px">
	            <fo:block><xsl:value-of select="vat_type"/></fo:block>
	        </fo:table-cell>
	    </fo:table-row>
	</xsl:template>

	<xsl:template match="amount|description|price|price_excl|price_vat|price_incl">
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

	<xsl:template match="sale_conditions_nl">
		<fo:block font-size="9pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Verkoopsvoorwaarden</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 1: Toepassingsgebied</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">Deze algemene voorwaarden gelden voor alle contracten afgesloten door VTK Ondersteuning vzw. De medecontractant wordt geacht ze te aanvaarden door het enkel feit van de ondertekening van het contract. Afwijking van deze verkoopsvoorwaarden, zelfs indien vermeld op documenten uitgaande van de medecontractant zijn alleen dan aan VTK Ondersteuning vzw tegenstelbaar wanneer zij door VTK Ondersteuning vzw schriftelijk werden bevestigd. In dat geval blijven alle overige verkoopsvoorwaarden van kracht waarvan niet uitdrukkelijk werd afgeweken.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 2: Totstandkoming van het contract</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">2.1. Alle mondelinge voorbesprekingen zijn zuiver informatief. De overeenkomst komt slechts tot stand door ondertekening van het contract door VTK Ondersteuning vzw. Een begin van uitvoering wordt gelijkgesteld met de afsluiting van een contract en met aanvaarding van deze algemene voorwaarden tenzij deze uitvoering onder uitdrukkelijk voorbehoud is geschied. De uitvoering ervan geschiedt conform de algemene verkoopsvoorwaarden in de offerte, het contract, de bestelbon, de leveringsnota, en/of de factuur opgenomen, zonder toepassing van de eigen voorwaarden van de medecontractant, zelfs al worden deze naderhand meegedeeld.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm">2.2. Elke annulering van de bestelling dient schriftelijk te geschieden. Zij is slechts geldig mits schriftelijke aanvaarding door VTK Ondersteuning vzw. Ingeval van annulering is de medecontractant een forfaitaire vergoeding van 35% verschuldigd van de prijs. Deze vergoeding dekt de vaste en variabele kosten en mogelijke winstderving.
		</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 3: Prijs</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">De prijs wordt bepaald op het ogenblik van de ondertekening van het contract.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 4: Levering</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">4.1. De goederen die materieel moeten worden geleverd (vb. boeken, e.d.), worden verstuurd per post, behoudens schriftelijk anders overeengekomen.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">4.2. Indien het contract toegang verleent tot een online-databank, heeft de levering plaats door overhandiging van een gebruikersnaam en wachtwoord.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 5: Controle</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.1. De medecontractant dient de goederen onmiddellijk in ontvangst te nemen en na te zien op hun conformiteit met de bestelling en op eventuele zichtbare gebreken. Indien op dat ogenblik niet wordt geprotesteerd, erkent de medecontractant dat de levering juist en volledig is, en aanvaardt hij de goederen in de staat waarin ze zich bevinden.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.2. Verborgen gebreken kunnen slechts tot vergoeding aanleiding geven indien zij binnen de 8 dagen kenbaar worden gemaakt aan VTK Ondersteuning VZW en dit bij aangetekend schrijven en de goederen inmiddels niet in behandeling worden genomen.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.3. De aansprakelijkheid van VTK Ondersteuning vzw is in elk geval beperkt tot de vervanging van de gebrekkige goederen door gelijkwaardige goederen. VTK Ondersteuning vzw is niet aansprakelijk voor enige andere schade uit welke hoofde ook, zij het aan personen, voorwerpen of aan de goederen zelf.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 6: Betalingen</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.1. De prijs is - behoudens uitdrukkelijk andersluidende vermelding op de factuur - betaalbaar uiterlijk 30 dagen na factuurdatum.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.2. Bij niet-betaling op de vervaldag zal van rechtswege en zonder voorafgaande ingebrekestelling een verwijlintrest verschuldigd zijn van 12 %, of , Indien deze hoger is, de wettelijke intrestvoet bepaald overeenkomstig artikel 5 van de Wet van 2 augustus 2002 betreffende de bestrijding van de betalingsachterstand bij handelstransacties,.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.3. Bij laattijdige betaling zal het factuurbedrag worden verhoogd met een forfaitaire schadevergoeding van 10 %, met een minimum van € 125, onverminderd het recht op een redelijke schadeloosstelling voor eventuele invorderingskosten overeenkomstig artikel 6 van de Wet van 2 augustus 2002. </fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.4. Verkeerde meldingen op de factuur moeten binnen de 8 dagen na de factuurdatum bij aangetekend schrijven worden meegedeeld. Na afloop van die termijn wordt de factuur geacht juist en aanvaard te zijn.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.5. In geval van betwisting van een deel van de geleverde goederen is de medecontractant in ieder geval gehouden tot betaling op de vervaldag van de factuur van het niet betwiste gedeelte.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 7: Waarborgen</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">Indien het vertrouwen van VTK Ondersteuning vzw in de kredietwaardigheid van de medecontractant geschokt wordt door daden van gerechtelijke uitvoering tegen de medecontractant en/of aanwijsbare andere gebeurtenissen die het vertrouwen in de goede uitvoering van door de medecontractant aangegane verbintenissen in vraag stellen, dan behoudt VTK Ondersteuning vzw zich het recht voor van de medecontractant geschikte waarborgen te eisen. Indien de medecontractant weigert hierop in te gaan, behoudt VTK Ondersteuning vzw zich het recht voor de gehele bestelling of een gedeelte ervan te annuleren, zelfs indien de goederen reeds geheel of gedeeltelijk werden verzonden of reeds online toegang werd verleend. In voorkomend geval zal een schadevergoeding verschuldigd zijn à rato van 35% van het bedrag van de bestelling/overeenkomst.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 8: Industriële en intellectuele eigendom</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">8.1. Indien een door VTK Ondersteuning vzw geleverd goed inbreuk zou maken op een octrooi of model recht of op andere rechten van industriële- of intellectuele eigendom van derden, zal VTK Ondersteuning vzw naar haar keuze en na overleg met de medecontractant het betreffende goed vervangen door een goed dat geen inbreuk maakt op het betrokken recht of een licentierecht terzake werven, dan wel het goed terugnemen tegen terugbetaling van de betaalde prijs, onder aftrek van een bedrag wegens slijtage en/of ouderdom. De medecontractant dient alleszins VTK Ondersteuning vzw tijdig en volledig in te lichten over de aanspraken van derden, op straffe van verlies van het recht op de hierboven vermelde prestaties.</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">8.2. Het is de medecontractant evenmin toegelaten om de gegevens waartoe toegang wordt verschaft of de publicaties die ter beschikking worden gesteld te verveelvoudigen of openbaar te maken door middel van druk, fotocopie, microfilm, elektronisch, op geluidsband of op welke andere wijze ook en evenmin in een retrieval systeem worden opgeborgen zonder voorafgaandelijke, uitdrukkelijke en schriftelijke toestemming van VTK Ondersteuning vzw.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 9: Privacy</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">De medecontractant verbindt er zich toe de persoonsgegevens waartoe hij toegang heeft gekregen naar aanleiding van de afsluiting van een contract met VTK Ondersteuning vzw, niet te kopiëren, openbaar te maken of over te dragen aan derden. Tevens verbindt de medecontractant er zich toe de wet betreffende de verwerking van persoonsgegevens d.d. 8 december 1992 te respecteren en na te leven.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 10: Overmacht</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">Ingeval van overmacht heeft VTK Ondersteuning vzw het recht om de uitvoering van de overeenkomst op te schorten hetzij de overeenkomst te beëindigen. Ingeval van overmacht ziet de medecontractant uitdrukkelijk af van enige schadevergoeding.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 11: Toepasselijk recht</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">Op alle door VTK Ondersteuning vzw afgesloten overeenkomsten zal uitsluitend het Belgisch recht van toepassing zijn.</fo:block>
		<fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
			Artikel 12: Geschillen</fo:block>
		<fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">Ingeval van betwisting zijn uitsluitend de Rechtbanken van Leuven bevoegd.</fo:block>

	</xsl:template>

	<xsl:template match="sub_entries">
	    <fo:block text-align="justify"><xsl:apply-templates/></fo:block>
	</xsl:template>

</xsl:stylesheet>
