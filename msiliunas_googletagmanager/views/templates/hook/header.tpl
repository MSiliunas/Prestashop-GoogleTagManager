{if isset($gtm_id) && $gtm_id}
  <script>
    var gtmId = '{$gtm_id}';
  </script>

  {literal}
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+gtmId+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','');</script>
  {/literal}  
{/if}
