<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="a">
        <fo:basic-link text-decoration="underline" color="blue">
            <xsl:attribute name="external-destination">
                <xsl:text>url('</xsl:text><xsl:value-of select="@href"/><xsl:text>')</xsl:text>
            </xsl:attribute>
            <fo:inline wrap-option="no-wrap"><xsl:value-of select="."/></fo:inline>
        </fo:basic-link>
    </xsl:template>

</xsl:stylesheet>
