{tr _0=$report_user}Report for %0{/tr}.{if $report_preferences.type eq 'html'}<br /><br />{/if}

{tr _0=$report_last_report_date}Last Report sent on %0.{/tr}{if $report_preferences.type eq 'html'}<br /><br />{/if}

{if $report_preferences.type eq 'html'}<u>{/if}{tr}Changes in detail:{/tr}{if $report_preferences.type eq 'html'}</u><br /><br />{else}

-----------------------
{/if}

{$report_body}{if $report_preferences.type eq 'html'}<br /><br />{/if}


{tr _0=$userWatchesUrl}You are receiving notification emails grouped in a periodic digest. To receive them individually when posted instead, change your preferences at %0{/tr}