<form action="index.php?action=NewsletterRegisterGuest" method="get">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.guestSubscription{/lang}</legend>
                
                <div class="formElement{if $errorField == 'email'} formError{/if}" id="emailDiv">
                    <div class="formFieldLabel">
                        <label for="email">{lang}wcf.acp.newsletter.subscriber.email{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="email" name="email" value="{$email}" />
                        {if $errorField == 'email'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'notUnique'}{lang}wcf.acp.newsletter.subscriber.email.error.notUnique{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                </div>
                <div class="formElement" id="checkboxDiv">
                    <div class="formFieldLabel">
                        <label for="checkbox">{lang}wcf.acp.newsletter.subscriber.email{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="checkbox" id="checkbox" name="checkbox" value="{$checkbox}" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="formSubmit">
        <input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
        <input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
        {@SID_INPUT_TAG}
    </div>
</form>