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
    <xsl:import href="footer.xsl"/>

    <xsl:import href="foreword.xsl"/>
    <xsl:import href="toc.xsl"/>
    <xsl:import href="cvgroup.xsl"/>
    <xsl:import href="index.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="cvbook">
        <fo:root font-size="{$font-size}pt" line-height="{$line-height}" font-family="Helvetica" text-align="justify">
            <fo:layout-master-set>

                <!-- Define master page layouts for both even and odd pages -->
                <fo:simple-page-master
                    master-name="page-even"
                    page-height="{$page-height}mm"
                    page-width="{$page-width}mm"
                    margin-top="{$margin-y}mm"
                    margin-bottom="{$margin-y}mm"
                    margin-left="{$margin-x}mm"
                    margin-right="{$margin-x}mm">

                    <fo:region-body margin-bottom="{$footer-height}mm"/>
                    <fo:region-after region-name="footer-even" extent="{$footer-height}mm"/>

                </fo:simple-page-master>

                <fo:simple-page-master
                    master-name="page-odd"
                    page-height="{$page-height}mm"
                    page-width="{$page-width}mm"
                    margin-top="{$margin-y}mm"
                    margin-bottom="{$margin-y}mm"
                    margin-left="{$margin-x}mm"
                    margin-right="{$margin-x}mm">

                    <fo:region-body margin-bottom="{$footer-height}mm"/>
                    <fo:region-after region-name="footer-odd" extent="{$footer-height}mm"/>

                </fo:simple-page-master>

                <!-- Define a page sequence, selecting the master page depending on whether the page number is odd or even -->
                <fo:page-sequence-master master-name="document">
                   <fo:repeatable-page-master-alternatives>
                       <fo:conditional-page-master-reference odd-or-even="even"
                         master-reference="page-even"/>
                       <fo:conditional-page-master-reference odd-or-even="odd"
                         master-reference="page-odd"/>
                   </fo:repeatable-page-master-alternatives>
                </fo:page-sequence-master>
            </fo:layout-master-set>

            <!-- Define the page content -->
            <fo:page-sequence master-reference="document">

                <!-- The footers: static content must be defined before flow content -->
                <!-- The footer for an even page -->
                <fo:static-content flow-name="footer-even">
                    <xsl:call-template name="footer-even"/>
                </fo:static-content>

                <!-- The footer for an odd page -->
                <fo:static-content flow-name="footer-odd">
                    <xsl:call-template name="footer-odd"/>
                </fo:static-content>

                <!-- The body -->
                <fo:flow flow-name="xsl-region-body">

                    <!-- Foreword -->
                    <xsl:apply-templates select="foreword"/>

                    <!-- Table of contents -->
                    <xsl:call-template name="toc"/>

                    <!-- The actual content: the cvs -->
                    <xsl:apply-templates select="cvs"/>

                    <!-- The alphabetical index -->
                    <xsl:call-template name="index"/>

                </fo:flow>

            </fo:page-sequence>
        </fo:root>
    </xsl:template>

</xsl:stylesheet>
