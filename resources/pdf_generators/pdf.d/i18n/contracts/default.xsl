<!--

Dutch translations

-->

<xsl:stylesheet version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--

Between <A> (known as "..") and <B> (known as "..") was agreed:

-->

<!-- "between" with upper case first letter -->
<xsl:template name="between_u">
    <xsl:text>Tussen</xsl:text>
</xsl:template>

<!-- "for" with upper case first letter -->
<xsl:template name="for_u">
    <xsl:text>Voor</xsl:text>
</xsl:template>

<!-- "and" all small letters -->
<xsl:template name="and">
    <xsl:text>en</xsl:text>
</xsl:template>

<!-- "represented by" -->
<xsl:template name="represented_by">
    <xsl:param name="name"/>
    <xsl:text>vertegenwoordigd door ondergetekende, </xsl:text><xsl:value-of select="$name"/><xsl:text>,</xsl:text>
</xsl:template>

<!-- "known as" -->
<xsl:template name="known_as">
    <xsl:param name="alias"/>
    <xsl:text>hierna genoemd "</xsl:text><xsl:value-of select="$alias"/><xsl:text>"</xsl:text>
</xsl:template>

<!-- "was agreed" -->
<xsl:template name="was_agreed">
    <xsl:text>werd het volgende overeengekomen:</xsl:text>
</xsl:template>

<!-- "the company" -->
<xsl:template name="the_company">
    <xsl:text>het bedrijf</xsl:text>
</xsl:template>

</xsl:stylesheet>

