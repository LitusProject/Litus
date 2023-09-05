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

    <xsl:import href="i18n/en.xsl"/>

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
                    <fo:table-cell><fo:block text-align="left">IBAN: BE30 7450 1759 0011 <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
                    <fo:table-cell><fo:block>Faculteitskring Ingenieurswetenschappen <xsl:apply-templates select="center"/></fo:block></fo:table-cell>
                    <fo:table-cell><fo:block text-align="right">http://www.vtk.be <xsl:apply-templates select="right"/></fo:block></fo:table-cell>
                </fo:table-row>
                <fo:table-row>
                    <fo:table-cell><fo:block text-align="left">BIC: KREDBEBB <xsl:apply-templates select="left"/></fo:block></fo:table-cell>
                    <fo:table-cell><fo:block>aan de KU Leuven <xsl:apply-templates select="center"/></fo:block></fo:table-cell>
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

    <xsl:template name="payment_days" match="payment_days">
        <xsl:value-of select="/invoice/@payment_days"/>
    </xsl:template>

    <xsl:template match="total">
        <fo:table table-layout="fixed" width="100%">
            <fo:table-column column-width="59%"/>
            <fo:table-column column-width="25%"/>
            <fo:table-column column-width="16%"/>
            <fo:table-column column-width="0%"/>

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
        <fo:block font-size="8pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            General Terms Vlaamse Technische Kring vzw</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 1: Scope</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">These general conditions apply to all contracts concluded by Vlaamse Technische Kring VZW. The contracting partner shall be deemed to accept by the mere fact of signing the contract. Deviation from these general terms, even if listed on documents from the contracting partner are only opposable to Vlaamse Technische Kring VZW when they were confirmed in writing by Vlaamse Technische Kring VZW. All other terms and conditions, from which is not explicitly deviated, remain in effect.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 2: Conclusion of the Contract</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">2.1. All oral discussions are purely informative. The agreement is only concluded by the signing of the contract by Vlaamse Technische Kring VZW. A beginning of execution is considered equivalent to the conclusion of a contract and to the acceptance of these general terms, unless the execution was done under explicit reservation. The execution shall be in accordance with the general terms, included in the tender, the contract, the order, the delivery note and/or the invoice, without application of the general terms of the contracting partner, even if these are communicated afterwards.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">2.2. Any cancelation of orders must be communicated in writing. It is only valid when accepted in writing by Vlaamse Technische Kring VZW. In case of cancelation, a lump sum depending on the subject of the agreement will be charged to the contracting partner. This lump sum covers the fixed and variable costs and possible loss of profit.
        </fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">2.2.1. For Sector Nights, BR Launches, Internship Fair, Jobfair or similar events, a cancellation up to 40 days before the event will induce a charge of 35% of the determined price, from 39 through 21 days before the event 60% of the determined price will be charged and cancellation starting at 20 days before an event will induce a charge of 100% of the agreed upon price.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">2.2.2. For goods or engagements contracted to VTK not affiliated with a physical event, a cancellation will induce a charge of 35% of the determined price.
        </fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 3: Price</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">3.1. The price is determined at the time of signing the Contract.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 4: Delivery</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">4.1. The goods to be delivered physically (e.g. books, etc.) are sent by post, unless otherwise agreed in writing.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">4.2.If the Contract provides access to an online database, the transfer is effected by submitting a username and password.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 5: Control</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">5.1. The contracting partner shall immediately accept the delivered goods and check their compliance with the order and on possible visible defects. By not protesting when accepting the goods, the contracting partner acknowledges that the delivery is correct and complete, and accepts the delivered goods in the condition they are delivered.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">5.2. Hidden defects can only lead to compensation when they are communicated to Vlaamse Technische Kring VZW by registered letter within 8 days after delivery, and only when the goods are not yet processed by the contracting partner.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">5.3. In any case, the liability of Vlaamse Technische Kring VZW is limited to the replacement of the faulty goods by equivalent goods. Vlaamse Technische Kring VZW is not liable for any other damages for whatever reason, be it to persons, objects or the goods themselves.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 6: Payments</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">6.1 The price is – unless explicitly stated otherwise on the invoice – to be paid no later than 30 days after the invoice date.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">6.2. In case of non-payment on the due date, by operation of law and without prior notice of default, the contracting partner will owe an interest of arrears of 12% or, in case of it being higher, the statutory interest rate in accordance with article 5 of the law of the 2nd of August 2002 concerning the prevention of late payments in trade transactions, amended by article 7 of the law of the 22nd of November 2013</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">6.3. In case of non-payment on the due date, by operation of law and without prior notice of default, the contracting partner will owe a flat rate of €40,- for the collection costs resulting from the non-payment, supplemented by a compensation of 10%, in accordance with article 6 of the law of the 2nd of August 2002 concerning the prevention of late payments in trade transactions, amended by article 8 of the law of the 22nd of November 2013</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">6.4. False notifications on the invoice are to be reported within 8 days after the invoice date by registered letter. After that period, the invoice is considered to be correct and accepted.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">6.5 In case of dispute on a part of the delivered goods, the contracting partner is in any case obliged to pay the non-disputed part of the invoice on the due date.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 7: Guarantees</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">If the solvency of the contracting partner has changed due to judicial acts against the contracting partner or other events which question the ability of the contracting partner to fulfill the obligations, Vlaamse Technische Kring VZW will retain the right to ask a adequate deposit from the contracting partner. If the contracting partner refuses to comply, Vlaamse Technische Kring VZW retains the right to cancel the order partially or completely, even if the goods have been shipped already, partially or completely, or if online access has been given already. Where appropriate, the contracting partners owes Vlaamse Technische Kring VZW a compensation of 35% of the total amount of the contract.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 8: Industrial and intellectual property</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">8.1 If a by Vlaamse Technische Kring VZW delivered good infringes a patent or industrial design right or any other right of industrial or intellectual property of a third party, Vlaamse Technische Kring VZW will, at its option and after consulting with the contracting partner, replace the concerned good by a good which does not infringe the concerned right or will acquire a license, or will withdraw the good against repayment of the paid sum, reduced by an amount for wear and/or age. The contracting partner will inform Vlaamse Technische Kring VZW timely and completely of claims by a third party, on penalty of losing the right of the above mentioned services.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">8.2 The contracting partner is not allowed to copy or publish by print, photocopy, microfilm, electronic, on audio tape or by any other means the data which has been given access to or the publications which have been put at the contracting partner’s disposal. Nor can the contracting partner store this data and/or publications in a retrieval system without prior written permission of Vlaamse Technische Kring VZW.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 9: Measures that force us to modify an event</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">If one or more subjects of this agreement are suscepted to measures taken or imposed by a government, the KU Leuven or another relevant authority, VTK will always try to maintain an event in a form similar to the original agreed upon concept. If these measures make it no longer possible to organise an event, an online alternative will be provided, depending on the type of event a compensation will be given.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">9.1. For Sector Nights, BR Launches or similar events, a conversion to an online event will induce a compensation in the form of a free option appointed by VTK from the Collaboration Brochure drawn up by VTK of the same academic year in which the contract was valid.</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">9.2. For Internship Fair, Jobfair or similar events, a conversion to an online event will induce a financial compensation of 25% of the price of the concerning option that requires physical presence.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 10: Force majeure</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm" text-align="justify">In case of force majeure, Vlaamse Technische Kring VZW has the right to suspend or cancel the execution of the contract. In case of force majeure, the contracting partner waives any damages.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 11: Applicable law</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">On all the contracts concluded by Vlaamse Technische Kring VZW exclusively the Belgian law is applicable.</fo:block>
        <fo:block font-size="7pt" font-family="sans-serif" padding-before="0.5mm" font-weight="bold">
            Article 12: Disputes</fo:block>
        <fo:block font-size="6pt" font-family="sans-serif" padding-before="0.5mm">In case of disputes the Courts of Leuven have exclusive jurisdiction</fo:block>

    </xsl:template>

    <xsl:template match="sub_entries">
        <fo:block text-align="justify"><xsl:apply-templates/></fo:block>
    </xsl:template>

</xsl:stylesheet>
