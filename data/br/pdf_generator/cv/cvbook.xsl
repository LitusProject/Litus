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
    <xsl:import href="cvgroup.xsl"/>
    <xsl:import href="toc.xsl"/>
    <xsl:import href="index.xsl"/>

    <xsl:output method="xml" indent="yes"/>

    <xsl:template match="cvbook">
        <fo:root font-size="{$font-size}pt" line-height="{$line-height}" font-family="Helvetica" text-align="justify">
            <fo:layout-master-set>
                <fo:simple-page-master master-name="page-master" page-height="{$page-height}mm" page-width="{$page-width}mm" margin-top="{$margin-y}mm" margin-bottom="{$margin-y}mm" margin-left="{$margin-x}mm" margin-right="{$margin-x}mm">
                    <fo:region-body/>
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

                    <xsl:call-template name="toc"/>

                    <xsl:apply-templates/>

                    <xsl:call-template name="index"/>

                </fo:flow>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>

</xsl:stylesheet>
