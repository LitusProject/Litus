<!--
    XSL Stylsheet for CV Book

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:import href="config.xsl"/>
    <xsl:import href="cv.xsl"/>

    <!-- A group of cvs -->
    <xsl:template match="cvgroup">
        <!-- A separation page -->
        <fo:block break-before="page">

            <!-- Set the id -->
            <xsl:attribute name="id">
                <xsl:value-of select="@name"/>
            </xsl:attribute>

            <!-- Set a marker: change the footer text to the group's name -->
            <fo:marker marker-class-name="footer-text">
                <xsl:value-of select="@name"/>
            </fo:marker>

            <!-- Terribly complex construct to vertically center something -->
            <fo:table table-layout="fixed" width="100%">
                <fo:table-column column-width="proportional-column-width(1)"/>
                <fo:table-body>
                    <!-- Make the row as high as the page content minus what will be in the row. This way, it is perfectly centered on the page -->
                    <fo:table-row height="{$content-height}mm - {$title-line-height} * {$title-font-size}pt">
                        <fo:table-cell display-align="center">

                            <fo:block
                                line-height="{$title-line-height}"
                                padding-top="2mm"
                                border-top-width="thick"
                                border-bottom-width="thick"
                                border-top-style="solid"
                                border-bottom-style="solid"
                                font-size="{$title-font-size}pt"
                                text-align="center">

                                <xsl:value-of select="@name"/>

                             </fo:block>

                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>
            </fo:table>

        </fo:block>

        <!-- Add the CVs -->
        <xsl:apply-templates/>
    </xsl:template>


</xsl:stylesheet>
