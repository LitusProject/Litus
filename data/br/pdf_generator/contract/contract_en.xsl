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

    <xsl:import href="i18n/en.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="contract">
        <fo:root font-size="10pt">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="8mm" margin-bottom="10mm" margin-left="20mm" margin-right="20mm">
                    <fo:region-body margin-bottom="8mm"/>
                    <fo:region-after region-name="footer-block" extent="10mm"/>
                </fo:simple-page-master>
                <fo:simple-page-master master-name="page-master-rest" page-height="297mm" page-width="210mm" margin-top="15mm" margin-bottom="10mm" margin-left="20mm" margin-right="20mm">
                    <fo:region-body margin-bottom="8mm"/>
                    <fo:region-after region-name="footer-block" extent="10mm"/>
                </fo:simple-page-master>

                <fo:page-sequence-master master-name="document">
                   <fo:repeatable-page-master-alternatives>
                       <fo:conditional-page-master-reference page-position="first"
                         master-reference="page-master"/>
                       <fo:conditional-page-master-reference page-position="rest"
                         master-reference="page-master-rest"/>
                       <fo:conditional-page-master-reference page-position="last"
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
                        <fo:block padding-after="8px">
                            <xsl:apply-templates select="above_sign"/>
                        </fo:block>
                        <fo:table table-layout="fixed" width="100%">
                            <fo:table-column column-width="50%"/>
                            <fo:table-column column-width="0%"/>
                            <fo:table-column column-width="40%"/>

                            <fo:table-body>
                                <fo:table-row height="3cm">
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
                                <fo:table-row height="3cm">
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

    <xsl:template name="date" match="date">
        <xsl:value-of select="/contract/@date"/>
    </xsl:template>

    <xsl:template name="location" match="location">
        <xsl:value-of select="/contract/@location"/>
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

    <xsl:template match="above_sign">
        <xsl:value-of select="/contract/above_sign"/>
        <xsl:call-template name="location"/>
        <xsl:value-of select="/contract/above_sign/@middle"/>
        <xsl:call-template name="date"/>
        <xsl:value-of select="/contract/above_sign/@end"/>
    </xsl:template>

    <xsl:template match="company_name">
        <xsl:value-of select="/contract/company/name"/>
    </xsl:template>

    <xsl:template match="title">
        <fo:block background-color="#DDDDDD" font-size="16pt" text-align="center" padding-top="5px" padding-bottom="5px" padding-left="0px" padding-right="0px" margin-left="0px" margin-right="0px">
            <xsl:apply-templates/>
        </fo:block>
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
                    <fo:table-cell><fo:block text-align="left">KBC: 745-01 75900-11 <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
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
            General Terms VTK Ondersteuning vzw</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 1: Scope</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">These general conditions apply to all contracts concluded by VTK Ondersteuning VZW. The contracting partner shall be deemed to accept by the mere fact of signing the contract. Deviation from these general terms, even if listed on documents from the contracting partner are only opposable to VTK Ondersteuning VZW when they were confirmed in writing by VTK Ondersteuning VZW. All other terms and conditions, from which is not explicitly deviated, remain in effect.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 2: Conclusion of the Contract</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">2.1. All oral discussions are purely informative. The agreement is only concluded by the signing of the contract by VTK Ondersteuning VZW. A beginning of execution is considered equivalent to the conclusion of a contract and to the acceptance of these general terms, unless the execution was done under explicit reservation. The execution shall be in accordance with the general terms, included in the tender, the contract, the order, the delivery note and/or the invoice, without application of the general terms of the contracting partner, even if these are communicated afterwards.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">2.2. Any cancelation of orders must be communicated in writing. It is only valid when accepted in writing by VTK Ondersteuning VZW. In case of cancelation, a lump sum of 35% of the price will be charged to the contracting partner. This lumped sum covers the fixed and variable costs and possible loss of profit.
        </fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 3: Price</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">3.1. The price is determined at the time of signing the Contract.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 4: Delivery</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">4.1. The goods to be delivered physically (e.g. books, etc.) are sent by post, unless otherwise agreed in writing.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">4.2.If the Contract provides access to an online database, the transfer is effected by submitting a username and password.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 5: Control</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.1. The contracting partner shall immediately accept the delivered goods and check their compliance with the order and on possible visible defects. By not protesting when accepting the goods, the contracting partner acknowledges that the delivery is correct and complete, and accepts the delivered goods in the condition they are delivered.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.2. Hidden defects can only lead to compensation when they are communicated to VTK Ondersteuning VZW by registered letter within 8 days after delivery, and only when the goods are not yet processed by the contracting partner.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">5.3. In any case, the liability of VTK Ondersteuning VZW is limited to the replacement of the faulty goods by equivalent goods. VTK Ondersteuning VZW is not liable for any other damages for whatever reason, be it to persons, objects or the goods themselves.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 6: Payments</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.1 The price is – unless explicitly stated otherwise on the invoice – to be paid no later than 30 days after the invoice date.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.2. In case of non-payment on the due date, the contracting partner shall be charged an interest on arrears of 12%, or the legal interest determined by article 5 of the Law of august 2, 2012 on combating payment arrears in commercial transactions, when the latter interest rate is higher, by operation of law and without prior notice.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.3. In case of late payments, the invoice price will be raised with a standard compensation of 10%, with a minimum of €125, without prejudice to the right on a reasonable compensation for any collection costs, in accordance with article 6 of the Law of august 2, 2012.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">6.4. False notifications on the invoice are to be reported within 8 days after the invoice date by registered letter. After that period, the invoice is considered to be correct and accepted.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm"> 6.5 In case of dispute on a part of the delivered goods, the contracting partner is in any case obliged to pay the non-disputed part of the invoice on the due date.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 7: Guarantees</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">If the solvency of the contracting partner has changed due to judicial acts against the contracting partner or other events which question the ability of the contracting partner to fulfill the obligations, VTK Ondersteuning VZW will retain the right to ask a adequate deposit from the contracting partner. If the contracting partner refuses to comply, VTK Ondersteuning VZW retains the right to cancel the order partially or completely, even if the goods have been shipped already, partially or completely, or if online access has been given already. Where appropriate, the contracting partners owes VTK Ondersteuning VZW a compensation of 35% of the total amount of the contract.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 8: Industrial and intellectual property</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">8.1 If a by VTK Ondersteuning VZW delivered good infringes a patent or industrial design right or any other right of industrial or intellectual property of a third party, VTK Ondersteuning VZW will, at its option and after consulting with the contracting partner, replace the concerned good by a good which does not infringe the concerned right or will acquire a license, or will withdraw the good against repayment of the paid sum, reduced by an amount for wear and/or age. The contracting partner will inform VTK Ondersteuning VZW timely and completely of claims by a third party, on penalty of losing the right of the above mentioned services.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">8.2 The contracting partner is not allowed to copy or publish by print, photocopy, microfilm, electronic, on audio tape or by any other means the data which has been given access to or the publications which have been put at the contracting partner’s disposal. Nor can the contracting partner store this data and/or publications in a retrieval system without prior written permission of VTK Ondersteuning VZW.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 9: Privacy</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">The contracting partner will not copy, publish or transfer to a third party the personal data which the contracting partner has been given access to following a contract with VTK Ondersteuning VZW. Also, the contracting partner will respect and comply with the law concerning the processing of personal data d.d. 8 December 1992 and GDPR regulations.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 10: Force majeure</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">In case of force majeure, VTK Ondersteuning VZW has the right to suspend or cancel the execution of the contract. In case of force majeure, the contracting partner waives any damages.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 11: Applicable law</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">On all the contracts concluded by VTK Ondersteuning VZW exclusively the Belgian law is applicable.</fo:block>
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 12: Disputes</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm">In case of disputes the Courts of Leuven have exclusive jurisdiction</fo:block>

    </xsl:template>

</xsl:stylesheet>
