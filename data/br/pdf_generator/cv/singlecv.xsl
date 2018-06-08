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
    <xsl:import href="cv.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="singlecv">
        <fo:root font-size="{$font-size}pt" line-height="{$line-height}" font-family="Helvetica" text-align="justify">
            <fo:layout-master-set>

                <!-- Define master page layouts -->
                <fo:simple-page-master
                    master-name="page-master"
                    page-height="{$page-height}mm"
                    page-width="200mm"
                    margin-top="{$margin-y}mm"
                    margin-bottom="{$margin-y}mm"
                    margin-left="{$margin-x}mm"
                    margin-right="{$margin-x}mm">

                    <fo:region-body margin-bottom="{$footer-height}mm"/>

                </fo:simple-page-master>
            </fo:layout-master-set>

            <!-- Define the page content -->
            <fo:page-sequence master-reference="page-master">

                <!-- The body -->
                <fo:flow flow-name="xsl-region-body">
                    <xsl:apply-templates/>

                </fo:flow>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>

</xsl:stylesheet>