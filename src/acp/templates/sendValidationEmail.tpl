{include file='header'}

<div class="mainHeadline">
    <img src="{@RELATIVE_WCF_DIR}icon/pmNewL.png" alt="" />
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.subscriber.sendValidationEmail{/lang}</h2>
    </div>
</div>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
    <p class="success">{lang}wcf.acp.newsletter.subscriber.sendValidationEmail.success{/lang}</p>
{/if}

<form method="post" action="index.php?form=SendValidationEmail">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.general{/lang}</legend>
                <div class="formElement{if $errorField == 'username'} formError{/if}" id="subjectDiv">
                    <div class="formFieldLabel">
                        <label for="username">{lang}wcf.user.username{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="username" name="username" value="{$username}" />
                        {if $errorField == 'username'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'notValid'}{lang}wcf.user.error.username.notValid{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="subjectHelpMessage">
                        <p>{lang}wcf.acp.newsletter.subscriber.sendValidationEmail.username.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('username');
                //]]></script>
            </fieldset>
        </div>
    </div>
    <div class="formSubmit">
        <input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
        <input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
        <input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
        {@SID_INPUT_TAG}
    </div>
</form>

{include file='footer'}