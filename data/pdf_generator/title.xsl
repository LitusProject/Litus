<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template name="title" match="title">
        <xsl:param name="text"/>
        <svg xmlns="http://www.w3.org/2000/svg" height="40" width="477.48">
            <rect x="11.61" y="6" height="16" fill="#000000" width="477.48">
            </rect>
            <text x="13" y="17" font-family="sans-serif" font-size="11" fill="#ffffff">
                <xsl:value-of select="$text"/>
            </text>
        </svg>
    </xsl:template>
    
</xsl:stylesheet>
