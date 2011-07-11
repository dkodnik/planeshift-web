<?xml version="1.0" encoding="ISO-8859-1"?>
<html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
<body>
<table>
<tr>
<xsl:for-each select="server_report/player[1]">
<xsl:for-each select="@*">
<th> <xsl:value-of select="name()"/> </th>
</xsl:for-each>
</xsl:for-each>
</tr>
<xsl:for-each select="server_report/player">
<tr>
<xsl:for-each select="@*">
<td>
<xsl:value-of select="."/> 
</td>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>

<table>
<tr>
<xsl:for-each select="server_report/npc[1]">
<xsl:for-each select="@*">
<th> <xsl:value-of select="name()"/> </th>
</xsl:for-each>
</xsl:for-each>
</tr>
<xsl:for-each select="server_report/npc">
<tr>
<xsl:for-each select="@*">
<td>
<xsl:value-of select="."/> 
</td>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>

</body>
</html>


