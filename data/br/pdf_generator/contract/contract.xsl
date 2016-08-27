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
                            <xsl:apply-templates select="payment_details"/>
                        </fo:block>
                        <fo:block padding-after="8px">
                            <xsl:apply-templates select="sub_entries"/>
                        </fo:block>
                        <fo:table table-layout="fixed" width="100%">
                            <fo:table-column column-width="50%"/>
                            <fo:table-column column-width="0%"/>
                            <fo:table-column column-width="40%"/>

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
                                        <fo:block padding-after="30px">
                                            <xsl:call-template name="for_u"/><xsl:text> </xsl:text><xsl:value-of select="our_union/@short_name"/><xsl:text>,</xsl:text>
                                            <fo:block/>
                                            <xsl:value-of select="our_union/@coordinator"/>
                                            <fo:block/>
                                            <xsl:call-template name="responsible_br"/>
                                        </fo:block>
                                    </fo:table-cell>
                                </fo:table-row>
                                <fo:table-row>
                                    <fo:table-cell>
                                        <fo:block>
                                        </fo:block>
                                    </fo:table-cell>
                                    <fo:table-cell>
                                       <fo:block/>
                                    </fo:table-cell>
                                    <fo:table-cell>
                                        <fo:block>
                                            <xsl:if test="our_union/@contact_person != our_union/@coordinator">
                                                <xsl:call-template name="for_u"/><xsl:text> </xsl:text><xsl:value-of select="our_union/@short_name"/><xsl:text>,</xsl:text>
                                                <fo:block/>
                                                <xsl:value-of select="our_union/@contact_person"/>
                                            </xsl:if>
                                        </fo:block>
                                    </fo:table-cell>
                                </fo:table-row>
                            </fo:table-body>
                        </fo:table>
                    </fo:block>
                    <fo:block break-after='page'/>
                    <xsl:apply-templates select="sale_conditions_nl"/>
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

    <xsl:template match="payment_details">
        <fo:block><xsl:apply-templates/></fo:block>
    </xsl:template>

    <xsl:template match="total_price">
        <xsl:apply-templates select="vat_total"/>
    </xsl:template>

    <xsl:template match="vat_total">
        <xsl:text>&#x20AC; </xsl:text>
        <xsl:value-of select="total"/>
        (excl. <xsl:value-of select="vat"/>% BTW),
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

    <xsl:template name="payment_days" match="payment_days">
        <xsl:value-of select="/contract/payment_details/@payment_days"/>
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

</xsl:stylesheet>
