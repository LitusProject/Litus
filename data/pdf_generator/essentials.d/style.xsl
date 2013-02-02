<xsl:stylesheet 
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="br">
        <xsl:choose>
            <xsl:when test="@padding-after">
                <fo:block>
                    <xsl:attribute name="padding-after"><xsl:value-of select="@padding-after"/></xsl:attribute>
                </fo:block>
            </xsl:when>
            <xsl:when test="@space">
                <fo:block>
                   <xsl:attribute name="padding-after"><xsl:value-of select="@space"/></xsl:attribute>
                </fo:block>
            </xsl:when>
            <xsl:otherwise>
                <fo:block/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="br">
        <xsl:param name="space"/>
        <xsl:choose>
            <xsl:when test="$space">
                <fo:block>
                    <xsl:attribute name="padding-after"><xsl:value-of select="$space"/></xsl:attribute>
                </fo:block>
            </xsl:when>
            <xsl:otherwise>
                <fo:block/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="ul">
        <fo:list-block>
            <xsl:apply-templates/>
        </fo:list-block>
    </xsl:template>

    <xsl:template match="ul/li">
        <fo:list-item>
            <fo:list-item-label end-indent="label-end()">
                <fo:block>&#x2022;</fo:block>
            </fo:list-item-label>
            <fo:list-item-body start-indent="5mm">
                <fo:block>
                    <xsl:apply-templates/>
                </fo:block>
            </fo:list-item-body>
        </fo:list-item>
    </xsl:template>

    <xsl:template match="p">
        <xsl:choose>
            <xsl:when test="@text-align">
                <fo:block>
                    <xsl:attribute name="text-align"><xsl:value-of select="@text-align"/></xsl:attribute>
                    <xsl:apply-templates/>
                </fo:block>
            </xsl:when>
            <xsl:otherwise>
                <fo:block>
                    <xsl:apply-templates/>
                </fo:block>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="page-break">
        <fo:block break-before="page"/>
    </xsl:template>

    <xsl:template match="b">
        <fo:inline font-weight="bold"><xsl:apply-templates/></fo:inline>
    </xsl:template>

    <xsl:template match="i">
        <fo:inline font-style="italic"><xsl:apply-templates/></fo:inline>
    </xsl:template>

    <xsl:template match="u">
        <fo:inline text-decoration="underline"><xsl:apply-templates/></fo:inline>
    </xsl:template>

    <xsl:template match="center">
        <fo:block text-align="center"><xsl:apply-templates/></fo:block>
    </xsl:template>

    <xsl:template match="single_quote">
        <xsl:text>'</xsl:text><xsl:apply-templates/><xsl:text>'</xsl:text>
    </xsl:template>

    <xsl:template match="quote">
        <xsl:text>"</xsl:text><xsl:apply-templates/><xsl:text>"</xsl:text>
    </xsl:template>

</xsl:stylesheet>
