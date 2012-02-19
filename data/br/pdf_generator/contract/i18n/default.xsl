<xsl:stylesheet
	version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

	<xsl:template name="between_u">
	    <xsl:text>Tussen</xsl:text>
	</xsl:template>
	
	<xsl:template name="for_u">
	    <xsl:text>Voor</xsl:text>
	</xsl:template>
	
	<xsl:template name="and">
	    <xsl:text>en</xsl:text>
	</xsl:template>
	
	<xsl:template name="represented_by">
	    <xsl:param name="name"/>
	    <xsl:text>vertegenwoordigd door ondergetekende, </xsl:text><xsl:value-of select="$name"/><xsl:text>,</xsl:text>
	</xsl:template>
	
	<xsl:template name="known_as">
	    <xsl:param name="alias"/>
	    <xsl:text>hierna genoemd "</xsl:text><xsl:value-of select="$alias"/><xsl:text>"</xsl:text>
	</xsl:template>
	
	<xsl:template name="was_agreed">
	    <xsl:text>werd het volgende overeengekomen:</xsl:text>
	</xsl:template>
	
	<xsl:template name="the_company">
	    <xsl:text>het bedrijf</xsl:text>
	</xsl:template>

</xsl:stylesheet>
