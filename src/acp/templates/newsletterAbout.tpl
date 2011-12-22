{include file="header"}

<div class="mainHeadline">
    {* <img src="{@RELATIVE_WCF_DIR}icon/cronjobsL.png" alt="" /> *}
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.about{/lang}</h2>
        <p>{lang}wcf.acp.newsletter.about.subtitle{/lang}</p>
    </div>
</div>

<div class="border content">
    <div class="container-1">
        <p>{lang}wcf.acp.newsletter.author{/lang}: Jim Martens</p>
        <p>{lang}wcf.acp.newsletter.copyright{/lang}: &copy; 2011 Jim Martens</p>
        <p>{lang}wcf.acp.newsletter.version{/lang}: {@$version} {if $update|isset && $update == true}[{lang}wcf.acp.newsletter.updateAvailable{/lang}]{/if}</p>
    </div>
</div>

{include file='footer'}