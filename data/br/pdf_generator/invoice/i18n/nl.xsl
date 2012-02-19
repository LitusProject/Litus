<xsl:stylesheet 
	version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

	<xsl:template name="invoice_all_u">
	    <xsl:text>FACTUUR</xsl:text>
	</xsl:template>
	
	<xsl:template name="invoice_address_u">
	    <xsl:text>Facturatieadres</xsl:text>
	</xsl:template>
	
	<xsl:template name="invoice_number_u">
	    <xsl:text>Factuurnummer</xsl:text>
	</xsl:template>
	
	<xsl:template name="invoice_date_u">
	    <xsl:text>Factuurdatum</xsl:text>
	</xsl:template>
	
	<xsl:template name="expiration_date_u">
	    <xsl:text>Vervaldatum</xsl:text>
	</xsl:template>
	
	<xsl:template name="vat_client_u">
	    <xsl:text>BTW-nummer Klant</xsl:text>
	</xsl:template>
	
	<xsl:template name="reference_u">
	    <xsl:text>Uw referentie</xsl:text>
	</xsl:template>
	
	<xsl:template name="description_u">
	    <xsl:text>Omschrijving</xsl:text>
	</xsl:template>
	
	<xsl:template name="total_excl_short">
	    <xsl:text>Totaal (Excl.)</xsl:text>
	</xsl:template>
	
	<xsl:template name="total_excl_full">
	    <xsl:text>TOTAAL (Excl. BTW)</xsl:text>
	</xsl:template>
	
	<xsl:template name="vat_all_u">
	    <xsl:text>BTW</xsl:text>
	</xsl:template>
	
	<xsl:template name="to_pay_all_u">
	    <xsl:text>TE BETALEN</xsl:text>
	</xsl:template>

</xsl:stylesheet>
