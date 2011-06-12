<?xml version="1.0" encoding="UTF-8"?>
<template defaultPart="main">

<part id="main"><![CDATA[
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="keywords" content="${header-keywords}" />
    <meta name="description" content="${header-description}" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>${header-title} | ${header-navigation}</title>
    <link href="${layout-css}" rel="stylesheet" type="text/css" media="screen" />

    <script type="text/javascript">
        CLE_nonRootPrefix = '${nonRootPrefix}';
        CLE__id = '${_id}';
        CLE_language = '${header-language}';
    </script>

    <script src="${nonRootPrefix}js/jquery.js" type="text/javascript" charset="utf-8"></script>
${header-add}
</head>
<body>
${message}
<h2>${header-navigation}</h2>

${content}

<div class="clearingDiv">&nbsp;</div>

<div id="footer">
    <p>
${footer}
    </p>
</div>
</body>
</html>
]]></part>

</template>