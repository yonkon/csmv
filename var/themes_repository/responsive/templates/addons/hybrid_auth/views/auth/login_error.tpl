<script type="text/javascript">
    {if $redirect_url}
        opener.location.href = '{$redirect_url|escape:javascript nofilter}';
    {else}
        opener.location.reload();
    {/if}

    close();
</script>