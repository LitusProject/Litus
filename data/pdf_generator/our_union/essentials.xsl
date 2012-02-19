<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="union_address">
        <fo:block>
            <fo:block font-weight="bold">
                <xsl:value-of select="name"/>
            </fo:block>
            <xsl:apply-templates select="address"/>
        </fo:block>
    </xsl:template>

    <xsl:template match="logo">
        <xsl:param name="width"><xsl:text>100%</xsl:text></xsl:param>
        <fo:external-graphic content-width="scale-to-fit" content-height="100%" scaling="uniform">
            <xsl:attribute name="width"><xsl:value-of select="$width"/></xsl:attribute>
            <xsl:attribute name="src"><xsl:text>url('</xsl:text><xsl:value-of select="."/><xsl:text>')</xsl:text></xsl:attribute>
        </fo:external-graphic>
    </xsl:template>
    
</xsl:stylesheet>

