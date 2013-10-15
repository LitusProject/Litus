<xsl:stylesheet
	version="1.0"
 	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:template match="company">
        <fo:block>
            <fo:block font-weight="bold">
                <xsl:value-of select="name"/>
                <xsl:if test="contact_person">
                    <fo:block/>
                    <xsl:apply-templates select="contact_person"/>
                </xsl:if>
            </fo:block>
            <xsl:apply-templates select="address"/>
        </fo:block>
    </xsl:template>

    <xsl:template match="contact_person">
        <xsl:choose>
            <xsl:when test="(last_name) or (first_name)">
                <xsl:if test="first_name"><xsl:value-of select="first_name"/></xsl:if>
                <xsl:if test="(first_name) and (last_name)"><xsl:text> </xsl:text></xsl:if>
                <xsl:if test="last_name"><xsl:value-of select="last_name"/></xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="."/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>
