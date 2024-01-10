<!--
    XSL Stylsheet for a single CV.

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:import href="config.xsl"/>

    <xsl:template match="cv">
        <fo:block break-before="page">

            <xsl:attribute name="id">
                <xsl:value-of select="@id"/>
            </xsl:attribute>

            <xsl:call-template name="basicinfo"/>
        </fo:block>

        <fo:block>
            <xsl:apply-templates select="section"/>
        </fo:block>
    </xsl:template>

    <xsl:template name="basicinfo">

        <fo:table>

            <fo:table-column column-width="65mm"/>
            <fo:table-column column-width="70mm"/>
            <fo:table-column column-width="25mm"/>

            <fo:table-body>
                <fo:table-row height="{$picture-ratio}*{$picture-width}mm">
                    <fo:table-cell>
                        <!-- First column of the basic info (name, email, phone) -->
                        <fo:block>
                            <xsl:call-template name="identification"/>
                        </fo:block>
                    </fo:table-cell>
                    <fo:table-cell>
                        <!-- Second column of the basic info (address) -->
                        <fo:block>
                            <xsl:apply-templates select="address"/>
                        </fo:block>
                    </fo:table-cell>
                    <fo:table-cell>
                        <!-- Third column of the basic info (photo) -->
                        <fo:block>
                            <xsl:call-template name="picture"/>
                        </fo:block>
                    </fo:table-cell>
                </fo:table-row>
            </fo:table-body>
        </fo:table>

        <fo:block>
            <xsl:apply-templates select="subsection"/>
        </fo:block>

    </xsl:template>

    <xsl:attribute-set name="largerFont">
        <xsl:attribute name="font-size">13pt</xsl:attribute>
        <xsl:attribute name="font-weight">bold</xsl:attribute>
    </xsl:attribute-set>

    <!-- The identification column -->
    <xsl:template name="identification">
        <fo:inline xsl:use-attribute-sets="largerFont">
            <xsl:value-of select="@firstname"/><xsl:text> </xsl:text><xsl:value-of select="@lastname"/>
        </fo:inline>
        <fo:block>
            <xsl:value-of select="@email"/>
        </fo:block>
        <fo:block>
            <xsl:value-of select="@phone"/>
        </fo:block>
        <fo:block>
            <xsl:value-of select="@birthday"/>
        </fo:block>
    </xsl:template>

    <!-- The address column -->
    <xsl:template match="address">
        <fo:block margin-top="17pt">
            <xsl:value-of select="@street"/><xsl:text> </xsl:text><xsl:value-of select="@nr"/>
            <xsl:choose>
                <xsl:when test="./@bus">
                    <xsl:text> (Bus </xsl:text><xsl:value-of select="@bus"/><xsl:text>)</xsl:text>
                </xsl:when>
            </xsl:choose>
        </fo:block>
        <fo:block>
            <xsl:value-of select="@postal"/><xsl:text> </xsl:text><xsl:value-of select="@city"/>
        </fo:block>
        <fo:block>
            <xsl:value-of select="@country"/>
        </fo:block>
    </xsl:template>

    <!-- The picture column -->
    <xsl:template name="picture">
        <fo:external-graphic content-width="{$picture-width}mm">
            <xsl:attribute name="src"><xsl:text>url('file:</xsl:text><xsl:value-of select="@img"/><xsl:text>')</xsl:text></xsl:attribute>
        </fo:external-graphic>
    </xsl:template>

    <!-- The studies table -->
    <xsl:template match="sec-special-studies">
        <fo:block text-align="left">

            <fo:table margin-right="0mm">

                <fo:table-column column-width="115mm"/>
                <fo:table-column column-width="35mm"/>
                <fo:table-column column-width="30mm"/>

                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@title_master"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@start_master"/> - <xsl:value-of select="@end_master"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <xsl:if test="string-length(@percentage_bach)!=0">
                                <fo:block><xsl:value-of select="@percentage_master"/></fo:block>
                            </xsl:if>
                        </fo:table-cell>
                    </fo:table-row>

                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@title_bach"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@start_bach"/> - <xsl:value-of select="@end_bach"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <xsl:if test="string-length(@percentage_bach)!=0">
                                <fo:block><xsl:value-of select="@percentage_bach"/></fo:block>
                            </xsl:if>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>

            </fo:table>

        </fo:block>
    </xsl:template>

    <!-- The erasmus section -->
    <xsl:template match="sec-special-erasmus">
        <xsl:value-of select="location"/> - <xsl:value-of select="period"/>
    </xsl:template>

    <xsl:template match="cv/section">
        <fo:block margin-top="{$section-top}mm">
            <!-- Section title -->
            <fo:inline font-weight="bold"><xsl:value-of select="@title"/></fo:inline>

            <fo:block margin-left="{$section-content-left}mm">
                <!-- Special subsections -->
                <xsl:apply-templates select="*[starts-with(name(), 'sec-special-')]"/>

                <!-- Section direct content -->
                <xsl:value-of select="content"/>

                <!-- Subsections -->
                <xsl:apply-templates select="subsection"/>
            </fo:block>

        </fo:block>
    </xsl:template>

    <!-- The Career table -->
    <xsl:template match="sec-special-career">
        <fo:block text-align="left">
            <fo:table start-indent="1mm">

                <fo:table-column column-width="80mm"/>
                <fo:table-column column-width="50mm"/>
                <fo:table-column column-width="50mm"/>

                <fo:table-header>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block font-style="italic"><xsl:value-of select="@EuropeHeader"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block font-style="italic"><xsl:value-of select="@WorldHeader"/></fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-header>

                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@EuropeContent"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@WorldContent"/></fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-body>

            </fo:table>
        </fo:block>
    </xsl:template>



    <!-- The languages table -->
    <xsl:template match="sec-special-languages">
        <fo:block>

            <!-- Move the table header to the title height (line height * font size) -->
            <fo:table margin-top="-{$line-height}*{$font-size}pt">

                <fo:table-column column-width="40mm"/>
                <fo:table-column column-width="40mm"/>
                <fo:table-column column-width="40mm"/>

                <fo:table-header>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block font-weight="bold"><xsl:value-of select="@oral"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block font-weight="bold"><xsl:value-of select="@written"/></fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-header>

                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@name1"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@oral1"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@written1"/></fo:block>
                        </fo:table-cell>
                    </fo:table-row>

                    <xsl:if test="string-length(@name2)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@name2"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@oral2"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@written2"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@name3)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@name3"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@oral3"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@written3"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@name4)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@name4"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@oral4"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@written4"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@name5)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@name5"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@oral5"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@written5"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>
                </fo:table-body>

            </fo:table>

        </fo:block>
    </xsl:template>

    <!-- The experiences table -->
    <xsl:template match="sec-special-experiences">
        <fo:block text-align="left">

            <fo:table margin-right="0mm">

                <fo:table-column column-width="20mm"/>
                <fo:table-column column-width="95mm"/>
                <fo:table-column column-width="35mm"/>

                <fo:table-body>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@experience_type1"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@experience_function1"/></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block><xsl:value-of select="@experience_start1"/> - <xsl:value-of select="@experience_end1"/></fo:block>
                        </fo:table-cell>
                    </fo:table-row>

                    <xsl:if test="string-length(@experience_function2)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_type2"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_function2"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_start2"/> - <xsl:value-of select="@experience_end2"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@experience_function3)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_type3"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_function3"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_start3"/> - <xsl:value-of select="@experience_end3"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@experience_function4)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_type4"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_function4"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_start4"/> - <xsl:value-of select="@experience_end4"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                    <xsl:if test="string-length(@experience_function5)!=0">
                        <fo:table-row>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_type5"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_function5"/></fo:block>
                            </fo:table-cell>
                            <fo:table-cell>
                                <fo:block><xsl:value-of select="@experience_start5"/> - <xsl:value-of select="@experience_end5"/></fo:block>
                            </fo:table-cell>
                        </fo:table-row>
                    </xsl:if>

                </fo:table-body>

            </fo:table>

        </fo:block>
    </xsl:template>

    <!-- A generic subsection -->
    <xsl:template match="subsection">

            <fo:block margin-top="{$subsection-top}mm">
                <!-- Subsection title -->
                <fo:inline font-style="italic"><xsl:value-of select="@title"/></fo:inline>

                <fo:block>
                    <!-- Subsection direct content -->
                    <xsl:value-of select="content"/>
                    <xsl:apply-templates select="sec-special-experiences"/>
                </fo:block>
            </fo:block>

    </xsl:template>

</xsl:stylesheet>
