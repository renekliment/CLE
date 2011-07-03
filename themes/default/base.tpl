<?xml version="1.0" encoding="UTF-8"?>
<template defaultPart="">

<part id="message"><![CDATA[
<div id="message">${message}</div>
]]></part>

<part id="row-b"><![CDATA[
<tr>
]]></part>

<part id="row-e"><![CDATA[
</tr>
]]></part>

<part id="paging-main"><![CDATA[
<br />
<p class="paging">
    ${data}
    <br /><i>(${items} ${what} {LNG_BASE_PAGING_TOGETHER})</i>
</p>
]]></part>

<part id="paging-value"><![CDATA[
<a href="?p=${page}${param}">${page}</a>&nbsp;
]]></part>

<part id="paging-bvalue"><![CDATA[
<b><u><a href="?p=${page}${param}">${page}</a></u></b>&nbsp;
]]></part>

<part id="paging-left"><![CDATA[
    <!-- <a href="?p=1${param}">{LNG_BASE_PAGING_FIRST}</a>&nbsp; -->
    <big><a href="?p=${page-previous}${param}">{LNG_BASE_PAGING_PREVIOUS}</a></big>&nbsp;&nbsp;&nbsp;
]]></part>

<part id="paging-right"><![CDATA[
    &nbsp;&nbsp;
    <big><a href="?p=${page-next}${param}">{LNG_BASE_PAGING_NEXT}</a></big>&nbsp;
    <!-- <a href="?p=${page-last}${param}">{LNG_BASE_PAGING_LAST}</a>&nbsp; -->
]]></part>

<part id="link"><![CDATA[
<a href="?r=http://${link-www}" target="_blank" title="">${link-name}</a>
]]></part>

<part id="email"><![CDATA[
<a href="mailto:${email}">${email-name}</a>
]]></part>

<part id="select-item"><![CDATA[
                <option value="${item-id}">${item-name}</option>
]]></part>

<part id="select-item-checking"><![CDATA[
                <option value="${item-id}"${item-selected}>${item-name}</option>
]]></part>

<part id="form-item-selected"><![CDATA[
selected="selected"
]]></part>

<part id="form-item-checked"><![CDATA[
checked="checked"
]]></part>

</template>