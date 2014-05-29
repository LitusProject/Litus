<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="our_union">
        <fo:block>
            <fo:block text-align="center">
                <xsl:apply-templates select="logo"/>
            </fo:block>
            <fo:block font-size="12pt" font-weight="bold" text-align="center">
                <xsl:apply-templates select="name"/>
            </fo:block>
        </fo:block>
    </xsl:template>

</xsl:stylesheet>
