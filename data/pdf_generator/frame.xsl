<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>    
    <xsl:template name="frame" match="frame">
        <xsl:param name="topmm"/>
        <xsl:param name="height"/>
        <xsl:param name="title"/>
        <xsl:param name="titlewidth"/>
        <fo:block-container position="absolute" left="0mm" width="162mm" border-width="0.1mm" border-color="black" border-style="solid" padding="1mm">
            <xsl:attribute name="top"><xsl:value-of select="concat ($topmm, 'mm')"/></xsl:attribute>
            <xsl:attribute name="height"><xsl:value-of select="$height"/></xsl:attribute>
            <fo:block padding="1.5mm" padding-before="3mm" padding-after="0mm" font-size="9pt" font-family="serif" line-height="12pt">
                <xsl:apply-templates select="*"/>
            </fo:block>
        </fo:block-container>
        <fo:block-container position="absolute" left="-5.2mm" width="30mm" height="20mm">
            <xsl:attribute name="top"><xsl:value-of select="concat(number($topmm) - 6.5 ,'mm')"/></xsl:attribute>
            <fo:block>
                <fo:instream-foreign-object>
                    <xsl:call-template name="title">
                        <xsl:with-param name="text" select="$title"/>
                        <xsl:with-param name="width" select="$titlewidth"/>
                    </xsl:call-template>
                </fo:instream-foreign-object>
            </fo:block>
        </fo:block-container>
    </xsl:template>
    
</xsl:stylesheet>
