{include file='header'}

<div class="mainHeadline">
    <img src="{@RELATIVE_WCF_DIR}icon/packageUpdateL.png" alt="" />
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.importSubscriber{/lang}</h2>
    </div>
</div>

{if $errorField != ''}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
    <p class="success">{lang}wcf.acp.newsletter.importSubscriber.success{/lang}</p>
{/if}

<form method="post" action="index.php?form=ImportSubscriber" enctype="multipart/form-data">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.importSubscriber.source{/lang}</legend>
            
                <div class="formElement{if $errorField == 'delimeter'} formError{/if}" id="delimeterDiv">
                    <div class="formFieldLabel">
                        <label for="delimeter">{lang}wcf.acp.newsletter.importSubscriber.delimeter{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="delimeter" name="delimeter" value="{@$delimeter}" />
                        {if $errorField == 'delimeter'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'tooLong'}{lang}wcf.acp.newsletter.importSubscriber.delimeter.error.tooLong{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="delimeterHelpMessage">
                        <p>{lang}wcf.acp.newsletter.importSubscriber.delimeter.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('delimeter');
                //]]></script>
                <div class="formElement{if $errorField == 'uploadFile'} formError{/if}" id="uploadFileDiv">
                    <div class="formFieldLabel">
                        <label for="uploadFile">{lang}wcf.acp.newsletter.importSubscriber.source.upload{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="file" id="uploadFile" name="uploadFile" value="" />
                        {if $errorField == 'uploadFile'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'uploadFailed'}{lang}wcf.acp.newsletter.importSubscriber.error.uploadFailed{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="uploadFileHelpMessage">
                        <p>{lang}wcf.acp.newsletter.importSubscriber.source.upload.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('uploadFile');
                //]]></script>
                
                <div class="formElement{if $errorField == 'downloadFile'} formError{/if}" id="downloadFileDiv">
                    <div class="formFieldLabel">
                        <label for="downloadFile">{lang}wcf.acp.newsletter.importSubscriber.source.download{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="downloadFile" name="downloadFile" value="" />
                        {if $errorField == 'downloadFile'}
                            <p class="innerError">
                                {if $errorType == 'notFound'}{lang}wcf.acp.newsletter.importSubscriber.error.notFound{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="downloadFileHelpMessage">
                        <p>{lang}wcf.acp.newsletter.importSubscriber.source.download.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('downloadFile');
                //]]></script>
                
            </fieldset>
            
            {if $additionalFields|isset}{@$additionalFields}{/if}
        </div>
    </div>

    <div class="formSubmit">
        <input type="submit" accesskey="s" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" />
        <input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
        <input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{$action}" />
    </div>
</form>