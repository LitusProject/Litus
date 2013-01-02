<!--
    XSL Stylsheet for CV Book

    TODO: erasmus

    @author Niels Avonds <niels.avonds@litus.cc>
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fo="http://www.w3.org/1999/XSL/Format"
>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="cvbook">
        <fo:root font-size="10pt" line-height="1.4" font-family="Helvetica">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="297mm" page-width="210mm" margin-top="8mm" margin-bottom="10mm" margin-left="13mm" margin-right="13mm">
                    <fo:region-body margin-bottom="8mm"/>
                </fo:simple-page-master>

                <fo:page-sequence-master master-name="document">
                   <fo:repeatable-page-master-alternatives>
                       <fo:conditional-page-master-reference odd-or-even="even"
                         master-reference="page-master"/>
                       <fo:conditional-page-master-reference odd-or-even="odd"
                         master-reference="page-master"/>
                   </fo:repeatable-page-master-alternatives>
                </fo:page-sequence-master>
            </fo:layout-master-set>

            <fo:page-sequence master-reference="document">
                <fo:flow flow-name="xsl-region-body">

                    <fo:block margin-left="20px" margin-right="20px">
                        <fo:block text-align="left">
                            <xsl:apply-templates/>
                        </fo:block>
                    </fo:block>
                </fo:flow>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>

    <xsl:template match="cv">
        <fo:block break-before="page">
            <xsl:call-template name="basicinfo"/>
        </fo:block>

        <xsl:apply-templates select="section"/>
    </xsl:template>

    <xsl:template name="basicinfo">

        <fo:table margin-left="-3.5mm">

            <fo:table-column column-width="67mm"/>
            <fo:table-column column-width="67mm"/>
            <fo:table-column column-width="30mm"/>

            <fo:table-body>
                <fo:table-row>
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

    </xsl:template>

    <xsl:template name="identification">
        <fo:inline font-weight="bold">
            <xsl:value-of select="@firstname"/><xsl:text> </xsl:text><xsl:value-of select="@lastname"/>
        </fo:inline>
        <fo:block>
            <xsl:value-of select="@email"/>
        </fo:block>
        <fo:block>
            <xsl:value-of select="@phone"/>
        </fo:block>
    </xsl:template>

    <xsl:template match="address">
        <fo:block>
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

    <xsl:template name="picture">
        <fo:external-graphic content-width="30mm">
            <xsl:attribute name="src">
                <xsl:value-of select="@img"/>
            </xsl:attribute>
        </fo:external-graphic>
    </xsl:template>

    <!-- The studies table -->
    <xsl:template match="sec-special-studies">
        <fo:block>

            <fo:table margin-left="-3mm" margin-right="0mm">

                <fo:table-column column-width="115mm"/>
                <fo:table-column column-width="35mm"/>
                <fo:table-column column-width="30mm"/>

                <fo:table-body>
                    <xsl:apply-templates select="study"/>
                </fo:table-body>

            </fo:table>

        </fo:block>
    </xsl:template>

    <!-- A single row in the study table -->
    <xsl:template match="study">
        <fo:table-row>
            <fo:table-cell>
                <fo:block><xsl:value-of select="title"/></fo:block>
            </fo:table-cell>
            <fo:table-cell>
                <fo:block><xsl:value-of select="@start"/> - <xsl:value-of select="@end"/></fo:block>
            </fo:table-cell>
            <fo:table-cell>
                <fo:block><xsl:value-of select="@percentage"/>%</fo:block>
            </fo:table-cell>
        </fo:table-row>
    </xsl:template>

    <!-- The languages table -->
    <xsl:template match="sec-special-languages">
        <fo:block>

            <!-- Move the table header to the title height (line height * font size) -->
            <fo:table margin-left="-3mm" margin-top="-1.5*10pt">

                <fo:table-column column-width="80mm"/>
                <fo:table-column column-width="50mm"/>
                <fo:table-column column-width="50mm"/>

                <fo:table-header>
                    <fo:table-row>
                        <fo:table-cell>
                            <fo:block></fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block font-weight="bold">Oral Skills</fo:block>
                        </fo:table-cell>
                        <fo:table-cell>
                            <fo:block font-weight="bold">Written Skills</fo:block>
                        </fo:table-cell>
                    </fo:table-row>
                </fo:table-header>

                <fo:table-body>
                    <xsl:apply-templates select="language"/>
                </fo:table-body>

            </fo:table>

        </fo:block>
    </xsl:template>

    <!-- A single row in the language table -->
    <xsl:template match="language">
        <fo:table-row>
            <fo:table-cell>
                <fo:block><xsl:value-of select="@name"/></fo:block>
            </fo:table-cell>
            <fo:table-cell>
                <fo:block><xsl:value-of select="@oral"/></fo:block>
            </fo:table-cell>
            <fo:table-cell>
                <fo:block><xsl:value-of select="@written"/></fo:block>
            </fo:table-cell>
        </fo:table-row>
    </xsl:template>

    <!-- The erasmus section -->
    <xsl:template match="sec-special-erasmus">
        <xsl:value-of select="location"/> - <xsl:value-of select="period"/>
    </xsl:template>

    <xsl:template match="cv/section">
        <fo:block margin-top="3mm">
            <!-- Section title -->
            <fo:inline font-weight="bold"><xsl:value-of select="@title"/></fo:inline>


            <fo:block margin-left="2mm">
                <!-- Special subsections -->
                <xsl:apply-templates select="*[starts-with(name(), 'sec-special-')]"/>

                <!-- Section direct content -->
                <xsl:value-of select="content"/>

                <!-- Subsections -->
                <xsl:apply-templates select="subsection"/>
            </fo:block>

        </fo:block>
    </xsl:template>

    <xsl:template match="subsection">

            <fo:block margin-top="1mm">
                <!-- Subsection title -->
                <fo:inline font-style="italic"><xsl:value-of select="@title"/></fo:inline>

                <fo:block>
                    <!-- Subsection direct content -->
                    <xsl:value-of select="content"/>
                </fo:block>
            </fo:block>

    </xsl:template>

</xsl:stylesheet>
