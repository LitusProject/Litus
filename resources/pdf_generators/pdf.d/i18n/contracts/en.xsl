<!--

This is the default language.

-->

<xsl:stylesheet version="1.0"
        xmlns:xls="http://www.w3.org/1999/XSL/Transform">

<!--

Between <A> (known as "..") and <B> (known as "..") was agreed:

-->

<!-- "between" with upper case first letter -->
<xsl:template name="between_u">
    <xsl:text>Between</xsl:text>
</xsl:template>

<!-- "for" with upper case first letter -->
<xsl:template name="for_u">
    <xsl:text>For</xsl:text>
</xsl:template>

<!-- "and" all small letters -->
<xsl:template name="and">
    <xsl:text>and</xsl:text>
</xsl:template>

<!-- "represented by" -->
<xsl:template name="represented_by">
    <xsl:param name="name"/>
    <xsl:text>represented by </xsl:text><xsl:value-of select="$name"/><xsl:text>,</xsl:text>
</xsl:template>

<!-- "known as" -->
<xsl:template name="known_as">
    <xsl:param name="alias"/>
    <xsl:text>known as "</xsl:text><xsl:value-of select="$alias"/><xsl:text>"</xsl:text>
</xsl:template>

<!-- "was agreed" -->
<xsl:template name="was_agreed">
    <xsl:text>was agreed</xsl:text>
</xsl:template>

<!-- "the company" -->
<xsl:template name="the_company">
    <xsl:text>the company</xsl:text>
</xsl:template>

</xsl:stylesheet>

