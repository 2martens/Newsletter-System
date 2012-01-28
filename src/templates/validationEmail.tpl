{include file="documentHeader"}
<head>
    <title>{@$subject}</title>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

<div id="main">
    <div class="content">
        <p>{@$content}</p>
    </div>
</div>

</body>
</html>