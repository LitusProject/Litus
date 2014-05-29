<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="address">
    	<fo:block text-align="left"><xsl:value-of select="street"/><xsl:text> </xsl:text><xsl:value-of select="number"/><xsl:if test="mailbox != ''"><xsl:text>/</xsl:text><xsl:value-of select="mailbox"/></xsl:if></fo:block>
    	<fo:block text-align="left"><xsl:value-of select="postal"/><xsl:text> </xsl:text><xsl:value-of select="city"/></fo:block>
    	<fo:block text-align="left"><xsl:value-of select="country"/></fo:block>
    </xsl:template>

</xsl:stylesheet>